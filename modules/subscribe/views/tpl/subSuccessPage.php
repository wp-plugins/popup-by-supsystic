<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<title><?php _e('Subscribe Confirmation', PPS_LANG_CODE)?></title>
	<style type="text/css">
		html, body {
			background-color: #f1f1f1;
			font-family: Lato,​ helvetica, sans-serif;
		}
		a {
			color: #2866ff;
				
		}
		.ppsConfirmMainShell {
			border: 1px solid #a1a1a1;
			border-radius: 6px;
			width: 540px;
			margin: 0 auto;
			background-color: #fff;
			text-align: center;
		}
		.ppsErrorMsg {
			color: #db3611;
		}
		.ppsConfirmContent, .ppsConfirmTitle {
			padding: 0 20px;
		}
		.ppsConfirmRedirectShell {
			background-color: #c1e1f7;
			border: 1px solid #7abeef;
			border-right: none;
			border-left: none;
			margin: 20px 0;
			padding: 20px 0;
		}
	</style>
</head>
<body>
	<div class="ppsConfirmMainShell">
		<?php if($this->res->error()) {
			$errors = $this->res->getErrors();
		?>
		<h1 class="ppsConfirmTitle"><?php _e('Some errors occured while trying to subscribe', PPS_LANG_CODE)?>:</h1>
		<div class="ppsConfirmContent">
			<div class="ppsErrorMsg"><?php echo implode('<br />', $errors)?></div>
		</div>
		<?php
		} else {
			$successMessage = $this->popup && isset($this->popup['params']['tpl']['sub_txt_success'])
				? $this->popup['params']['tpl']['sub_txt_success']
				: __('Thank you for subscribe!', PPS_LANG_CODE);
			$redirectUrl = isset($this->popup['params']['tpl']['sub_redirect_url']) && !empty($this->popup['params']['tpl']['sub_redirect_url'])
				? $this->popup['params']['tpl']['sub_redirect_url']
				: get_bloginfo('wpurl');
			$redirectUrl = trim($redirectUrl);
			if(strpos($redirectUrl, 'http') !== 0) {
				$redirectUrl = 'http://'. $redirectUrl;
			}
			$autoRedirectTime = 10;
		?>
		<h1 class="ppsConfirmTitle"><?php _e('Subscription confirmed', PPS_LANG_CODE)?></h1>
		<div class="ppsConfirmContent">
			<?php echo $successMessage;?>
		</div>
		<div class="ppsConfirmRedirectShell">
			<?php printf(__('<a href="%s">Back to site</a> in <i id="ppsConfirmBackCounter">%d</i> seconds'), $redirectUrl, $autoRedirectTime)?>
		</div>
		<script type="text/javascript">
			var ppsAutoRedirectTime = <?php echo $autoRedirectTime;?>
			,	ppsAutoRedirectTimeLeft = ppsAutoRedirectTime;
			function ppsAutoRedirectWaitClb() {
				ppsAutoRedirectTime--;
				if(ppsAutoRedirectTime > 0) {
					document.getElementById('ppsConfirmBackCounter').innerHTML = ppsAutoRedirectTime;
					setTimeout(ppsAutoRedirectWaitClb, 1000);
				} else {
					window.location.href = '<?php echo $redirectUrl?>';
				}
			}
			setTimeout(ppsAutoRedirectWaitClb, 1000);
		</script>
		<?php
		}?>
	</div>
</body>
</html>