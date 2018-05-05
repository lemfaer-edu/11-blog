<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface as EM;

class SecurityController extends Controller {

	/**
	 * Renders login form
	 */
	function login(AuthorizationCheckerInterface $check) {
		if (true === $check->isGranted("IS_AUTHENTICATED_FULLY")) {
			return $this->redirectToRoute("blog_list");
		}

		return $this->render("user/login.html.twig");
	}

	/**
	 * Renders registration form
	 */
	function register(AuthorizationCheckerInterface $check) {
		if (true === $check->isGranted("IS_AUTHENTICATED_FULLY")) {
			return $this->redirectToRoute("blog_list");
		}

		return $this->render("user/registration.html.twig");
	}

	/**
	 * Performs actions to add new user to app
	 */
	function register_submit(EM $em, Request $request, UserPasswordEncoderInterface $encoder) {
		$captcha_secret = $this->getParameter("captcha_secret");
		$captcha_token = $request->get("g-recaptcha-response");
		$captcha_response = $this->captchaverify($captcha_secret, $captcha_token);
		$captcha_valid = $captcha_response["success"];

		if (!$captcha_valid) {
			$this->redirectToRoute("blog_list");
		}

		$user = new User;
		$user->login = $request->get("login");
		$user->public = $request->get("login");
		$user->email = $request->get("email");
		$user->roles = array("ROLE_USER");
		$user->data = array();

		$password = $request->get("password");
		$password = $encoder->encodePassword($user, $password);
		$user->password = $password;

		$em->persist($user);
		$em->flush();

		// login
		return $this->redirectToRoute("blog_list");
	}

	/**
	 * Verifies google recapcha response
	 * @param string $secret secret key
	 * @param string $response value of 'g-recaptcha-response'
	 * @return array
	 */
	function captchaverify($secret, $response) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, compact("secret", "response"));
		$response = curl_exec($ch);
		curl_close($ch);

		return json_decode($response, true);
	}

}
