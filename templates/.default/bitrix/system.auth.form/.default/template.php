<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CJSCore::Init();

global $APPLICATION;

$formClass = (!empty($arParams['IS_AJAX']) && 'Y' == $arParams['IS_AJAX']) ? 'col-sm-10 offset-sm-1' : 'col-sm-4 offset-sm-4';

?>
<div class="auth-form row mt-5">
    <form name="system_auth_form<?=$arResult["RND"]?>" class="<?= $formClass ?>" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
        <div class="auth-form__errors text-danger mb-2" data-entity="error-messages">
    	<?if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR']) {
    		ShowMessage($arResult['ERROR_MESSAGE']);
        }?>
        </div>

        <label class="wide">
            <input class="form-control" type="text" placeholder="Логин или эл. почта" autocomplete="username" name="USER_LOGIN" maxlength="50">
            <script>
				BX.ready(function() {
					var loginCookie = BX.getCookie("<?=CUtil::JSEscape($arResult["~LOGIN_COOKIE_NAME"])?>");
					if (loginCookie)
					{
						var form = document.forms["system_auth_form<?=$arResult["RND"]?>"];
						var loginInput = form.elements["USER_LOGIN"];
						loginInput.value = loginCookie;
					}
				});
			</script>
        </label>

        <label class="wide">
            <input class="form-control" type="password" placeholder="Введите пароль" autocomplete="current-password" name="USER_PASSWORD" maxlength="50"><!-- autocomplete="off" -->
            <?if($arResult["SECURE_AUTH"]):?>
	            <span class="bx-auth-secure" id="bx_auth_secure<?=$arResult["RND"]?>" title="<?echo GetMessage("AUTH_SECURE_NOTE")?>" style="display:none">
	            	<div class="bx-auth-secure-icon"></div>
	            </span>
	            <noscript>
	            	<span class="bx-auth-secure" title="<?echo GetMessage("AUTH_NONSECURE_NOTE")?>">
	            		<div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
	            	</span>
	            </noscript>
	            <script type="text/javascript">
	            	document.getElementById('bx_auth_secure<?=$arResult["RND"]?>').style.display = 'inline-block';
	            </script>
            <?endif?>
        </label>

        <div class="container-fluid">
            <div class="auth-form__helpers row justify-content-between">
            	<?if ($arResult["STORE_PASSWORD"] == "Y"):?>
            	<label title="<?=GetMessage("AUTH_REMEMBER_ME")?>" class="form-check">
            		<input class="remember form-check-input" type="checkbox" name="USER_REMEMBER" value="Y" id="USER_REMEMBER_frm" />
            		<span class="form-check-label">запомнить меня</span>
            	</label>
            	<?endif?>
            	<noindex><a class="forgot ar" href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow">Забыли пароль?</a></noindex>
            </div>
        </div>

        <?if ($arResult["CAPTCHA_CODE"]):?>
            <?echo GetMessage("AUTH_CAPTCHA_PROMT")?>:<br />
            <input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
            <img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /><br /><br />
            <input type="text" name="captcha_word" maxlength="50" value="" />
        <?endif?>

        <div class="auth-form__submit-wrap">
        	<input class="btn btn-primary aligncenter" type="submit" name="Login" value="Войти" />
        </div>

        <?if($arResult["AUTH_SERVICES"]):?>
        <div class="auth-form__social-login mt-4">
        	<span>Войти с помощью:</span>
            <?$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "flat",
            	array(
            		"AUTH_SERVICES"=>$arResult["AUTH_SERVICES"],
            		"SUFFIX"=>"form",
            	),
            	$component->__parent,
            	array("HIDE_ICONS"=>"Y")
            );?>
        </div>
        <?endif?>

        <?if($arResult["BACKURL"] <> ''):?>
        <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
        <?endif?>
        <?foreach ($arResult["POST"] as $key => $value):?>
        <input type="hidden" name="<?=$key?>" value="<?=$value?>" />
        <?endforeach?>
        <input type="hidden" name="AUTH_FORM" value="Y" />
        <input type="hidden" name="TYPE" value="AUTH" />

        <?if($arResult["NEW_USER_REGISTRATION"] == "Y"):?>
        <div class="col-sm-4 offset-sm-4 mt-2 text-center">
            <noindex><a class="register" href="<?=$arResult["AUTH_REGISTER_URL"]?>" rel="nofollow">Регистрация</a></noindex>
        </div>
        <?endif?>
    </form>
</div>
<?if(!empty($arParams['IS_AJAX']) && 'Y' == $arParams['IS_AJAX']):?>
<script>
    <?
    $arParamsToDelete = array(
        "login",
        "login_form",
        "logout",
        "register",
        "forgot_password",
        "change_password",
        "confirm_registration",
        "confirm_code",
        "confirm_user_id",
        "logout_butt",
        "auth_service_id",
        "fancybox"
    );
    ?>
    var authFormTarget = '[name="system_auth_form<?=$arResult["RND"]?>"]',
        authFormErrorsTarget = authFormTarget + ' [data-entity="error-messages"]',
        authRefferLink = '<?echo $APPLICATION->GetCurPageParam("", $arParamsToDelete);?>';

    jQuery(document).ready(function($) {
        var $form = $(authFormTarget),
        $errors = $(authFormErrorsTarget);

        $form.on('submit', function () {
            $errors.hide();

            $.post('/local/components/nikolays93/custom.auth/ajax.php', $form.serialize(), function (response) {

                if (response && response.STATUS)
                {
                    if ('OK' == response.STATUS) {
                        $form.after( response.HTML );
                        $form.remove();
                    }
                    else {
                        $errors
                            .html(response.MESSAGES)
                            .html(response.HTML)
                            .fadeIn(100);
                    }
                }

            }, 'json');

            return false;
        });
    });
</script>
<?endif;?>
<?/*?>
<div class="bx-system-auth-form">
<?if($arResult["FORM_TYPE"] == "otp"):
?>

<form name="system_auth_form<?=$arResult["RND"]?>" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
<?if($arResult["BACKURL"] <> ''):?>
	<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
<?endif?>
	<input type="hidden" name="AUTH_FORM" value="Y" />
	<input type="hidden" name="TYPE" value="OTP" />
	<table width="95%">
		<tr>
			<td colspan="2">
			<?echo GetMessage("auth_form_comp_otp")?><br />
			<input type="text" name="USER_OTP" maxlength="50" value="" size="17" autocomplete="off" /></td>
		</tr>
<?if ($arResult["CAPTCHA_CODE"]):?>
		<tr>
			<td colspan="2">
			<?echo GetMessage("AUTH_CAPTCHA_PROMT")?>:<br />
			<input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
			<img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /><br /><br />
			<input type="text" name="captcha_word" maxlength="50" value="" /></td>
		</tr>
<?endif?>
<?if ($arResult["REMEMBER_OTP"] == "Y"):?>
		<tr>
			<td valign="top"><input type="checkbox" id="OTP_REMEMBER_frm" name="OTP_REMEMBER" value="Y" /></td>
			<td width="100%"><label for="OTP_REMEMBER_frm" title="<?echo GetMessage("auth_form_comp_otp_remember_title")?>"><?echo GetMessage("auth_form_comp_otp_remember")?></label></td>
		</tr>
<?endif?>
		<tr>
			<td colspan="2"><input type="submit" name="Login" value="<?=GetMessage("AUTH_LOGIN_BUTTON")?>" /></td>
		</tr>
		<tr>
			<td colspan="2"><noindex><a href="<?=$arResult["AUTH_LOGIN_URL"]?>" rel="nofollow"><?echo GetMessage("auth_form_comp_auth")?></a></noindex><br /></td>
		</tr>
	</table>
</form>

<?
else:
?>

<form action="<?=$arResult["AUTH_URL"]?>">
	<table width="95%">
		<tr>
			<td align="center">
				<?=$arResult["USER_NAME"]?><br />
				[<?=$arResult["USER_LOGIN"]?>]<br />
				<a href="<?=$arResult["PROFILE_URL"]?>" title="<?=GetMessage("AUTH_PROFILE")?>"><?=GetMessage("AUTH_PROFILE")?></a><br />
			</td>
		</tr>
		<tr>
			<td align="center">
			<?foreach ($arResult["GET"] as $key => $value):?>
				<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
			<?endforeach?>
			<input type="hidden" name="logout" value="yes" />
			<input type="submit" name="logout_butt" value="<?=GetMessage("AUTH_LOGOUT_BUTTON")?>" />
			</td>
		</tr>
	</table>
</form>
<?endif?>
</div>
*/