<?php

namespace App\Repository;

use App\CanReceiveEmails;
use Carbon\Carbon;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class VerifiedTokenRepository
{
    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $connection;

    /**
     * The Hasher implementation.
     *
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hasher;

    /**
     * The token database table.
     *
     * @var string
     */
    protected $table;

    /**
     * The hashing key.
     *
     * @var string
     */
    protected $hashKey;

    /**
     * The number of seconds a token should last.
     *
     * @var int
     */
    protected $expires;

    /**
     * Create a new token repository instance.
     *
     * @param  \Illuminate\Database\ConnectionInterface  $connection
     * @param  \Illuminate\Contracts\Hashing\Hasher  $hasher
     * @param  string  $table
     * @param  string  $hashKey
     * @param  int  $expires
     * @return void
     */
    public function __construct(ConnectionInterface $connection, HasherContract $hasher,
                                $table, $hashKey, $expires = 1440)
    {
        $this->table = $table;
        $this->hasher = $hasher;
        $this->hashKey = $hashKey;
        $this->expires = $expires * 60;
        $this->connection = $connection;
    }

    /**
     * Create a new token record.
     *
     * @param CanReceiveEmails $user
     * @return string
     */
    public function create($user)
    {
        $email = $user->routeNotificationFor('mail');

        $this->deleteExisting($user);

        // We will create a new, random token for the user so that we can e-mail them
        // a safe link to the password reset form. Then we will insert a record in
        // the database so that we can verify the token within the actual reset.
        $token = $this->createNewToken();

        $this->getTable()->insert($this->getPayload($email, $token));

        return $token;
    }

    /**
     * Delete all existing reset tokens from the database.
     *
     * @param CanReceiveEmails $user
     * @return int
     */
    protected function deleteExisting($user)
    {
        return $this->getTable()
            ->where('email', $user->routeNotificationFor('mail'))
            ->whereNull('used_at')
            ->delete();
    }

    /**
     * Build the record payload for the table.
     *
     * @param  string  $email
     * @param  string  $token
     * @return array
     */
    protected function getPayload($email, $token)
    {
        return ['email' => $email, 'token' => $this->hasher->make($token), 'created_at' => new Carbon];
    }

    /**
     * Determine if a token record exists and is valid.
     *
     * @param CanReceiveEmails $user
     * @param  string $token
     * @return bool
     */
    public function exists($user, $token)
    {
        $record = (array) $this->getTable()->where(
            'email', $user->routeNotificationFor('mail')
        )->whereNull('used_at')->first();

        if ($record &&
            ! $this->tokenExpired($record['created_at']) &&
            $this->hasher->check($token, $record['token'])) {

            $this->getTable()->where('token', $record['token'])
                ->where('email', $record['email'])
                ->update(['used_at' => now()]);
            return true;
        }

        return false;
    }

    /**
     * Determine if the token has expired.
     *
     * @param  string  $createdAt
     * @return bool
     */
    protected function tokenExpired($createdAt)
    {
        return Carbon::parse($createdAt)->addSeconds($this->expires)->isPast();
    }

    /**
     * Delete a token record by user.
     *
     * @param CanReceiveEmails $user
     * @return void
     */
    public function delete($user)
    {
        $this->deleteExisting($user);
    }

    /**
     * Delete expired tokens.
     *
     * @return void
     */
    public function deleteExpired()
    {
        $expiredAt = Carbon::now()->subSeconds($this->expires);

        $this->getTable()
            ->where('created_at', '<', $expiredAt)
            ->whereNull('used_at')
            ->delete();
    }

    /**
     * Create a new token for the user.
     *
     * @return string
     */
    public function createNewToken()
    {
        return hash_hmac('sha256', str_random(40), $this->hashKey);
    }

    /**
     * Get the database connection instance.
     *
     * @return \Illuminate\Database\ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Begin a new database query against the table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getTable()
    {
        return $this->connection->table($this->table);
    }

    /**
     * Get the hasher instance.
     *
     * @return \Illuminate\Contracts\Hashing\Hasher
     */
    public function getHasher()
    {
        return $this->hasher;
    }
}
