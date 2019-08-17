<?php
Yii::setAlias('@resourceCountryCity', realpath(dirname(__FILE__).'/../../resource/country_city'));
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'user.passwordResetTokenExpire' => 3600,
    'siteForecast' => 'http://quiz.dev.travelinsides.com/forecast/api/getForecast',
];
