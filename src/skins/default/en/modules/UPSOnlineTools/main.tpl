<widget module="UPSOnlineTools" target="profile" template="common/dialog.tpl" head="Address error" body="modules/UPSOnlineTools/suggestion.tpl" IF="showAV" name="checkForm"/>
<widget module="UPSOnlineTools" target="checkout" mode="register" template="common/dialog.tpl" body="modules/UPSOnlineTools/suggestion.tpl" head="Address error" name="checkForm" allowAnonymous="{config.General.enable_anon_checkout}" IF="showAV"/>
