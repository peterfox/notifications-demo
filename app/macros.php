<?php

\Illuminate\Database\Schema\Blueprint::macro('flag', function($column) {
    $this->timestamp($column)->nullable();
});

\Illuminate\Database\Schema\Blueprint::macro('emailFlags', function() {
    $this->flag('email_verified_at');
    $this->flag('email_subscribed_at');
});