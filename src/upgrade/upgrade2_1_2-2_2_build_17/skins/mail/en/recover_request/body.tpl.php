<?php
    $find_str = <<<EOT
{* E-mail sent to customer when he requests lost account information *}
<html>
<body>
<p>{config.Company.company_name}: Help system

<p>You have requested your lost account information

<p>To confirm your recover password request, simply click on the link below:<br>
<a href="{url}">{url:h}</a>

<p>Or, copy, paste and open the following URL in your favorite browser:<br>
{url:h}

<br>
EOT;
    $replace_str = <<<EOT
{* E-mail sent to customer when he requests lost account information *}
<html>
<body>
<p>{config.Company.company_name}: Automated help-desk system

<p>You are receiving this e-mail message because you have requested to recover your forgotten password.<br>
If you did not submit such a request, it might mean that somebody was trying to gain access to your account at {config.Company.company_name}.

<p>To confirm that this request was submitted by you, click on the link below:<br>
<a href="{url}">{url:h}</a>

<p>Alternatively, you can copy and paste the link URL into the 'Location' field of your browser.<br>
Once you confirm the request, an e-mail message containing your new password will be sent to you.

<br>
EOT;
    $source = strReplace($find_str, $replace_str, $source, __FILE__, __LINE__);
?>
