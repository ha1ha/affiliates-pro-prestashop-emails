<?php
/**
 * AuthController override which will use a dedicated email notification template
 * for new affiliates instead of sending the new customer email provided by default.
 * 
 * @author itthinx
 */
class AuthController extends AuthControllerCore {

	/**
	 * sendConfirmationMail
	 * @param Customer $customer
	 * @return bool
	 */
	protected function sendConfirmationMail(Customer $customer)
	{
		if (!Configuration::get('PS_CUSTOMER_CREATION_EMAIL')) {
			return true;
		}
	
		$is_affiliate = false;
		// At this stage we can't do this because the affiliate entry is not yet created.
		//if (function_exists('affiliates_user_is_affiliate')) {
		//	$is_affiliate = affiliates_user_is_affiliate($customer->id);
		//}
		// ... so instead, we check if the customer requested to sign up for the affiliate program:
		if (isset($_POST['aff']) && ($_POST['aff'] == '1')) {
			$is_affiliate = true;
		}

		if ($is_affiliate) {

			$affiliate_area_url =
				Tools::getHttpHost(true).
				__PS_BASE_URI__.
				'index.php?fc=module&module=affiliatesprops&controller=affiliatearea';

			$result = Mail::Send(
				$this->context->language->id,
				'account-affiliates',
				Mail::l('Welcome to the Affiliate Program!'),
				array(
					'{firstname}' => $customer->firstname,
					'{lastname}' => $customer->lastname,
					'{email}' => $customer->email,
					'{passwd}' => Tools::getValue('passwd'),
					'{affiliate_area_url}' => $affiliate_area_url
				),
				$customer->email,
				$customer->firstname.' '.$customer->lastname
			);
		} else {
			$result = Mail::Send(
				$this->context->language->id,
				'account',
				Mail::l('Welcome!'),
				array(
					'{firstname}' => $customer->firstname,
					'{lastname}' => $customer->lastname,
					'{email}' => $customer->email,
					'{passwd}' => Tools::getValue('passwd')),
				$customer->email,
				$customer->firstname.' '.$customer->lastname
			);
		}
	}
}
