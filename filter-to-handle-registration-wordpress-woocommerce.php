<?php


// Add filter to handle registration
add_filter('registration_errors', 'custom_registration_errors', 10, 3);

function custom_registration_errors($errors, $sanitized_user_login, $user_email) {
    // Extensive list of allowed email domains, including popular local providers from Poland and international providers
    $allowed_domains = array(
        'gmail.com', 'yahoo.com', 'outlook.com', // International providers
        'wp.pl', 'onet.pl', 'interia.pl', 'o2.pl', // Popular Polish providers
        'poczta.fm', 'gazeta.pl', 'agora.pl', 'home.pl' // Additional popular Polish providers
    );

    // Get the email domain of the user
    list($email_user, $email_domain) = explode('@', $user_email);
    
    // List of EU country code top-level domains (ccTLDs)
    $eu_ccTLDs = [
        'at', 'be', 'bg', 'cy', 'cz', 'de', 'dk', 'ee', 'es', 'fi',
        'fr', 'gr', 'hr', 'hu', 'ie', 'it', 'lt', 'lu', 'lv', 'mt',
        'nl', 'pl', 'pt', 'ro', 'se', 'si', 'sk'
    ];

    // Extract the domain extension
    $domain_extension = strtolower(substr(strrchr($email_domain, '.'), 1));

    // Check if the user is logged in and is not an administrator
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        if (!in_array('administrator', (array) $current_user->roles)) {
            // Allow registration if the email domain is in the allowed list or ends with an EU ccTLD
            if (!in_array($email_domain, $allowed_domains) && !in_array($domain_extension, $eu_ccTLDs)) {
                $errors->add('email_domain_error', __('Registration is allowed only from specific email domains or any domain corresponding to EU country codes.'));
            }
        }
    }

    return $errors;
}