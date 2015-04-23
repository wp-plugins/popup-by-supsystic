<div class="ppsPopupOptRow">
	<label>
		<?php echo htmlPps::checkbox('params[tpl][enb_subscribe]', array(
			'checked' => htmlPps::checkedOpt($this->popup['params']['tpl'], 'enb_subscribe'),
			'attrs' => 'data-switch-block="subShell"',
		))?>
		<?php  _e('Enable Subscription', PPS_LANG_CODE)?>
	</label>
</div>
<span data-block-to-switch="subShell">
	<table class="form-table ppsSubShellMainTbl" style="width: auto;">
		<tr>
			<th scope="row">
				<?php _e('Subscribe to', PPS_LANG_CODE)?>
				<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Destination for your Subscribers.', PPS_LANG_CODE))?>"></i>
			</th>
			<td>
				<?php echo htmlPps::selectbox('params[tpl][sub_dest]', array(
					'options' => $this->subDestListForSelect, 
					'value' => (isset($this->popup['params']['tpl']['sub_dest']) ? $this->popup['params']['tpl']['sub_dest'] : '')))?>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<?php _e('Subscribe with Facebook', PPS_LANG_CODE)?>
				<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(sprintf(__('Add button to your PopUp with possibility to subscribe just in one click - without filling fields in your subscribe form, <img src="%s" />', PPS_LANG_CODE), $this->promoModPath. 'img/fb-subscribe.jpg'))?>"></i>
				<?php if(!$this->isPro) {?>
					<span class="ppsProOptMiniLabel"><a target="_blank" href="<?php echo $this->mainLink. '?utm_source=plugin&utm_medium=fb_subscribe&utm_campaign=popup';?>"><?php _e('PRO option', PPS_LANG_CODE)?></a></span>
				<?php }?>
			</th>
			<td>
				<?php echo htmlPps::checkbox('params[tpl][sub_enb_fb_subscribe]', array(
					'attrs' => 'class="ppsProOpt"',
					'checked' => htmlPps::checkedOpt($this->popup['params']['tpl'], 'sub_enb_fb_subscribe')))?>
			</td>
		</tr>
		<tr class="ppsPopupSubDestOpts ppsPopupSubDestOpts_wordpress">
			<th scope="row">
				<?php _e('Create user after subscribe with role', PPS_LANG_CODE)?>
				<i class="fa fa-question supsystic-tooltip" title="<?php echo _e('Use this only if you are really need it. Remember! After you change this option - your new subscriber will have more privileges than usual subscribers, so be careful with this option!', PPS_LANG_CODE)?>"></i>
			</th>
			<td>
				<?php echo htmlPps::selectbox('params[tpl][sub_wp_create_user_role]', array(
					'options' => $this->availableUserRoles,
					'value' => (isset($this->popup['params']['tpl']['sub_wp_create_user_role']) ? $this->popup['params']['tpl']['sub_wp_create_user_role'] : 'subscriber')))?>
			</td>
		</tr>
		<tr class="ppsPopupSubDestOpts ppsPopupSubDestOpts_wordpress">
			<th scope="row">
				<?php _e('Create Subscriber without confirmation', PPS_LANG_CODE)?>
				<i class="fa fa-question supsystic-tooltip" title="<?php echo _e('Usually, after user subscribe, we send email with confirmation link - to confirm email addres, and only after usee will click on link from this email - we will create new subscriber. This option allow you to create subscriber - right after subscription, without email confirmation process.', PPS_LANG_CODE)?>"></i>
			</th>
			<td>
				<?php echo htmlPps::checkbox('params[tpl][sub_ignore_confirm]', array(
					'checked' => htmlPps::checkedOpt($this->popup['params']['tpl'], 'sub_ignore_confirm')))?>
			</td>
		</tr>
		<tr class="ppsPopupSubDestOpts ppsPopupSubDestOpts_aweber">
			<th scope="row">
				<?php _e('Aweber Unique List ID', PPS_LANG_CODE)?>
				<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(sprintf(__('Check <a href="%s" target="_blank">this page</a> for more details', PPS_LANG_CODE), 'https://help.aweber.com/hc/en-us/articles/204028426-What-Is-The-Unique-List-ID-'))?>"></i>
			</th>
			<td>
				<?php echo htmlPps::text('params[tpl][sub_aweber_listname]', array(
					'value' => (isset($this->popup['params']['tpl']['sub_aweber_listname']) ? $this->popup['params']['tpl']['sub_aweber_listname'] : '')))?>
			</td>
		</tr>
		<tr class="ppsPopupSubDestOpts ppsPopupSubDestOpts_mailchimp">
			<th scope="row">
				<?php _e('MailChimp API key', PPS_LANG_CODE)?>
				<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(sprintf(__('To find your MailChimp API Key login to your mailchimp account at <a href="%s" target="_blank">%s</a> then from the left main menu, click on your Username, then select "Account" in the flyout menu. From the account page select "Extras", "API Keys". Your API Key will be listed in the table labeled "Your API Keys". Copy / Paste your API key into "MailChimp API key" field here.', PPS_LANG_CODE), 'http://mailchimp.com', 'http://mailchimp.com'))?>"></i>
			</th>
			<td>
				<?php echo htmlPps::text('params[tpl][sub_mailchimp_api_key]', array(
					'value' => (isset($this->popup['params']['tpl']['sub_mailchimp_api_key']) ? $this->popup['params']['tpl']['sub_mailchimp_api_key'] : ''),
					'attrs' => 'style="min-width: 300px;"'))?>
			</td>
		</tr>
		<tr class="ppsPopupSubDestOpts ppsPopupSubDestOpts_mailchimp">
			<th scope="row">
				<?php _e('Lists for subscribe', PPS_LANG_CODE)?>
				<i class="fa fa-question supsystic-tooltip-bottom" title="<?php _e('Select lists for subscribe. They are taken from your MailChimp account - so make sure that you entered correct API key before.', PPS_LANG_CODE)?>"></i>
			</th>
			<td>
				<div id="ppsMailchimpListsShell" style="display: none;">
					<?php echo htmlPps::selectlist('params[tpl][sub_mailchimp_lists]', array(
						'value' => (isset($this->popup['params']['tpl']['sub_mailchimp_lists']) ? $this->popup['params']['tpl']['sub_mailchimp_lists'] : ''),
						//'options' => array('' => __('Loading...', PPS_LANG_CODE)),
						'attrs' => 'id="ppsMailchimpLists" class="chosen" data-placeholder="'. __('Choose Lists', PPS_LANG_CODE). '"',
					))?>
				</div>
				<span id="ppsMailchimpNoApiKey"><?php _e('Enter API key - and your list will appear here', PPS_LANG_CODE)?></span>
				<span id="ppsMailchimpMsg"></span>
			</td>
		</tr>
		<tr class="ppsPopupSubDestOpts ppsPopupSubDestOpts_mailchimp">
			<th scope="row">
				<?php _e('Disable double opt-in', PPS_LANG_CODE)?>
				<i class="fa fa-question supsystic-tooltip" title="<?php echo _e('Disable double opt-in confirmation message sending - will create subscriber directly after he will sign-up to your form.', PPS_LANG_CODE)?>"></i>
			</th>
			<td>
				<?php echo htmlPps::checkbox('params[tpl][sub_dsbl_dbl_opt_id]', array(
					'checked' => htmlPps::checkedOpt($this->popup['params']['tpl'], 'sub_dsbl_dbl_opt_id')))?>
			</td>
		</tr>
		<tr class="ppsPopupSubDestOpts ppsPopupSubDestOpts_mailpoet">
			<?php if($this->mailPoetAvailable) { ?>
				<th scope="row">
					<?php _e('MailPoet Subscribe Lists', PPS_LANG_CODE)?>
				</th>
				<td>
					<?php if(!empty($this->mailPoetListsSelect)) { ?>
						<?php echo htmlPps::selectbox('params[tpl][sub_mailpoet_list]', array(
							'value' => (isset($this->popup['params']['tpl']['sub_mailpoet_list']) ? $this->popup['params']['tpl']['sub_mailpoet_list'] : ''),
							'options' => $this->mailPoetListsSelect,
							/*'attrs' => 'style="min-width: 300px;"'*/))?>
					<?php } else { ?>
						<div class="description"><?php printf(__('You have no subscribe lists, <a target="_blank" href="%s">create lists</a> at first, then - select them here.', PPS_LANG_CODE), admin_url('admin.php?page=wysija_subscribers&action=addlist'))?></div>
					<?php }?>
				</td>
			<?php } else { ?>
				<th scope="row" colspan="2">
					<div class="description"><?php printf(__('To use this subscribe engine - you must have <a target="_blank" href="%s">MailPoet plugin</a> installed on your site', PPS_LANG_CODE), admin_url('plugin-install.php?tab=search&s=MailPoet'))?></div>
				</th>
			<?php }?>
		</tr>
	</table>
	<div class="ppsPopupOptRow">
		<fieldset class="ppoPopupSubFields" style="padding: 10px;">
			<legend><?php _e('Subscription fields', PPS_LANG_CODE)?></legend>
			<label class="supsystic-tooltip" title="Email field is mandatory for most of subscribe engines - so it should be always enabled">
				<?php echo htmlPps::checkbox('enabled_email_subscribe', array('checked' => 1, 'attrs' => 'disabled'))?>
				<?php _e('Email', PPS_LANG_CODE)?>
			</label>
			<label>
				<?php echo htmlPps::checkbox('params[tpl][enb_sub_name]', array('checked' => htmlPps::checkedOpt($this->popup['params']['tpl'], 'enb_sub_name')))?>
				<?php _e('Name', PPS_LANG_CODE)?>
			</label>
		</fieldset>
	</div>
	<table class="form-table ppsSubShellOptsTbl">
		<tr class="ppsPopupSubTxtsAndRedirect" style="display: none;">
			<th scope="row">
				<?php _e('"Confirmation sent" message', PPS_LANG_CODE)?>
				<i class="fa fa-question supsystic-tooltip" title="<?php echo _e('This will be message, that user will see after subscribe - that email with confirmation link sent.', PPS_LANG_CODE)?>"></i>
			</th>
			<td>
				<?php echo htmlPps::text('params[tpl][sub_txt_confirm_sent]', array(
					'value' => (isset($this->popup['params']['tpl']['sub_txt_confirm_sent']) ? esc_html( $this->popup['params']['tpl']['sub_txt_confirm_sent'] ) : __('Confirmation link was sent to your email address. Check your email!', PPS_LANG_CODE)),
				))?>
			</td>
		</tr>
		<tr class="ppsPopupSubTxtsAndRedirect" style="display: none;">
			<th scope="row">
				<?php _e('Subscribe success message', PPS_LANG_CODE)?>
				<i class="fa fa-question supsystic-tooltip" title="<?php echo _e('Right after subscriber will be created and confirmed - this message will be shown.', PPS_LANG_CODE)?>"></i>
			</th>
			<td>
				<?php echo htmlPps::text('params[tpl][sub_txt_success]', array(
					'value' => (isset($this->popup['params']['tpl']['sub_txt_success']) ? esc_html( $this->popup['params']['tpl']['sub_txt_success'] ) : __('Thank you for subscribe!', PPS_LANG_CODE)),
				))?>
			</td>
		</tr>
		<tr class="ppsPopupSubTxtsAndRedirect" style="display: none;">
			<th scope="row">
				<?php _e('Email error message', PPS_LANG_CODE)?>
				<i class="fa fa-question supsystic-tooltip" title="<?php echo _e('If email, that was entered by user, is invalid - user will see this message', PPS_LANG_CODE)?>"></i>
			</th>
			<td>
				<?php echo htmlPps::text('params[tpl][sub_txt_invalid_email]', array(
					'value' => (isset($this->popup['params']['tpl']['sub_txt_invalid_email']) ? esc_html( $this->popup['params']['tpl']['sub_txt_invalid_email'] ) : __('Empty or invalid email', PPS_LANG_CODE)),
				))?>
			</td>
		</tr>
		<tr class="ppsPopupSubRedirect">
			<th scope="row">
				<?php _e('Redirect after subscription URL', PPS_LANG_CODE)?>
				<i class="fa fa-question supsystic-tooltip" title="<?php echo _e('You can enable redirection after subscription, just enter here URL that you want to redirect to after subscribe - and user will be redirected there. If you don\'t need this feature - just leave this field empty.', PPS_LANG_CODE)?>"></i>
			</th>
			<td>
				<?php echo htmlPps::text('params[tpl][sub_redirect_url]', array(
					'value' => (isset($this->popup['params']['tpl']['sub_redirect_url']) ? esc_url( $this->popup['params']['tpl']['sub_redirect_url'] ) : ''),
				))?>
			</td>
		</tr>
		<tr class="ppsPopupSubEmailTxt" style="display: none;">
			<th scope="row">
				<?php _e('Confirmation email subject', PPS_LANG_CODE)?>
				<i class="fa fa-question supsystic-tooltip" title="<?php echo _e('Email with confirmation link subject', PPS_LANG_CODE)?>"></i>
			</th>
			<td>
				<?php echo htmlPps::text('params[tpl][sub_txt_confirm_mail_subject]', array(
					'value' => esc_html ( isset($this->popup['params']['tpl']['sub_txt_confirm_mail_subject']) 
						? $this->popup['params']['tpl']['sub_txt_confirm_mail_subject'] 
						: __('Confirm subscription on [sitename]', PPS_LANG_CODE)),
				))?>
			</td>
		</tr>
		<tr class="ppsPopupSubEmailTxt" style="display: none;">
			<th scope="row">
				<?php _e('Confirmation email From field', PPS_LANG_CODE)?>
				<i class="fa fa-question supsystic-tooltip" title="<?php echo _e('Email with confirmation link From field', PPS_LANG_CODE)?>"></i>
			</th>
			<td>
				<?php echo htmlPps::text('params[tpl][sub_txt_confirm_mail_from]', array(
					'value' => esc_html ( isset($this->popup['params']['tpl']['sub_txt_confirm_mail_from']) 
						? $this->popup['params']['tpl']['sub_txt_confirm_mail_from'] 
						: $this->adminEmail),
				))?>
			</td>
		</tr>
		<tr class="ppsPopupSubEmailTxt" style="display: none;">
			<th scope="row">
				<?php _e('Confirmation email text', PPS_LANG_CODE)?>
				<i class="fa fa-question supsystic-tooltip" title="<?php echo _e('Email with confirmation link content', PPS_LANG_CODE)?>"></i>
				<?php $allowVarsInMail = array('sitename', 'siteurl', 'confirm_link');?>
				<div class="description"><?php printf(__('You can use next variables here: %s', PPS_LANG_CODE), '['. implode('], [', $allowVarsInMail).']')?></div>
			</th>
			<td>
				<?php echo htmlPps::textarea('params[tpl][sub_txt_confirm_mail_message]', array(
					'value' => esc_html( isset($this->popup['params']['tpl']['sub_txt_confirm_mail_message']) 
						? $this->popup['params']['tpl']['sub_txt_confirm_mail_message'] 
						: __('You subscribed on site <a href="[siteurl]">[sitename]</a>. Follow <a href="[confirm_link]">this link</a> to complete your subscription. If you did not subscribe here - just ignore this message.', PPS_LANG_CODE)),
				))?>
			</td>
		</tr>
		<tr class="ppsPopupSubEmailTxt" style="display: none;">
			<th scope="row">
				<?php _e('New Subscriber email subject', PPS_LANG_CODE)?>
				<i class="fa fa-question supsystic-tooltip" title="<?php echo _e('Email to New Subscriber subject', PPS_LANG_CODE)?>"></i>
			</th>
			<td>
				<?php echo htmlPps::text('params[tpl][sub_txt_subscriber_mail_subject]', array(
					'value' => esc_html ( isset($this->popup['params']['tpl']['sub_txt_subscriber_mail_subject']) 
						? $this->popup['params']['tpl']['sub_txt_subscriber_mail_subject'] 
						: __('[sitename] Your username and password', PPS_LANG_CODE)),
				))?>
			</td>
		</tr>
		<tr class="ppsPopupSubEmailTxt" style="display: none;">
			<th scope="row">
				<?php _e('New Subscriber email From field', PPS_LANG_CODE)?>
				<i class="fa fa-question supsystic-tooltip" title="<?php echo _e('New Subscriber email From field', PPS_LANG_CODE)?>"></i>
			</th>
			<td>
				<?php echo htmlPps::text('params[tpl][sub_txt_subscriber_mail_from]', array(
					'value' => esc_html ( isset($this->popup['params']['tpl']['sub_txt_subscriber_mail_from']) 
						? $this->popup['params']['tpl']['sub_txt_subscriber_mail_from'] 
						: $this->adminEmail),
				))?>
			</td>
		</tr>
		<tr class="ppsPopupSubEmailTxt" style="display: none;">
			<th scope="row">
				<?php _e('New Subscriber email text', PPS_LANG_CODE)?>
				<i class="fa fa-question supsystic-tooltip" title="<?php echo _e('Email to New Subscriber content', PPS_LANG_CODE)?>"></i>
				<?php $allowVarsInMail = array('user_login', 'user_email', 'password', 'login_url', 'sitename', 'siteurl');?>
				<div class="description" style=""><?php printf(__('You can use next variables here: %s', PPS_LANG_CODE), '['. implode('], [', $allowVarsInMail).']')?></div>
			</th>
			<td>
				<?php echo htmlPps::textarea('params[tpl][sub_txt_subscriber_mail_message]', array(
					'value' => esc_html( isset($this->popup['params']['tpl']['sub_txt_subscriber_mail_message']) 
						? $this->popup['params']['tpl']['sub_txt_subscriber_mail_message'] 
						: __('Username: [user_login]<br />Password: [password]<br />[login_url]', PPS_LANG_CODE)),
				))?>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<?php _e('Submit button name', PPS_LANG_CODE)?>
			</th>
			<td>
				<?php echo htmlPps::text('params[tpl][sub_btn_label]', array('value' => $this->popup['params']['tpl']['sub_btn_label']))?>
			</td>
		</tr>
	</table>
</span>