<?php
/**
 * Tests for Figuren_Theater Security module.
 *
 * phpcs:disable WordPress.Files, WordPress.NamingConventions, PSR1.Classes.ClassDeclaration.MissingNamespace, HM.Functions.NamespacedFunctions
 */

/**
 * Test security module renders correctly.
 */
class SecurityCest {

	/**
	 * Security link is shown, and page renders correctly.
	 *
	 * @param AcceptanceTester $I Tester
	 */
	public function testSecurityLink( AcceptanceTester $I ) {
		$I->wantToTest( 'Security link is shown, and page renders correctly.' );

		$I->resizeWindow( 1200, 800 );

		$I->loginAsAdmin();
		$I->amOnAdminPage( '/' );

		// See the Security link in menu.
		$I->seeLink( 'Security' );

		// Click the link to open the security.
		$I->click( 'Security' );

		// Doc sets are visible in submenu.
		$I->seeLink( 'Developer Security' );
		$I->seeLink( 'User Guides' );

		// See the main title.
		$I->seeElement( '.figuren-theater-ui__doc-title' );

		// Click to go to CMS Module docs.
		$I->see( 'CMS', 'li' );
		$I->click( 'CMS' );

		// See the CMS H1 title.
		$I->see( 'CMS', 'h1' );

		// User guide section is visible.
		$I->click( 'User Guides' );
		$I->see( 'User Guides', 'h1' );
	}

}
