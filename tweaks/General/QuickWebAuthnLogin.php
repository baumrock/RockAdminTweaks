<?php

namespace RockAdminTweaks;

use ProcessWire\HookEvent;

/**
 * If there is only 1 superuser with TfaWebAuthn set up on a debug mode site
 * and if the username is empty on login submission, we change the login_name
 * to the superuser's account name, and the login_pass to a dummy value to
 * allow the Session::authenticate hook to be called.
 *
 * Although the Session::authenticate method will fail we will hookAfter it
 * and pretend that it didn't. That will start 2FA credential collection
 * allowing us to proceed with just that protecting the account.
 *
 * For local installs where I have a YubiKey, this will be good enough.
 */

final class QuickWebAuthnLogin extends Tweak
{
    private $supers;
    private $superuser;

    public function info(): array
    {
        return [
            'description' => 'Allow quick login as unique SuperUser, in debug mode, using just WebAuthn credentials',
        ];
    }


    public function ready(): void
    {
        if ($this->wire->fields->get('tfa_type') === null) {
            return; // No 2FA set up
        }
        if (!$this->wire->config->debug) {
            return; // Not a debug mode site
        }

        $super_role_id = $this->wire->config->superUserRolePageID;
        $this->supers = $this->wire->users->find("roles=$super_role_id, tfa_type=TfaWebAuthn");

        if (1 !== count($this->supers)) {
            return; // Not exactly 1 TfaWebAuthn superuser
        }

        $this->superuser = $this->supers->eq(0);

        if ('TfaWebAuthn' !== $this->superuser->hasTfa()) {
            return; // TfaWebAuthn Not fully configured for superuser
        }

        $this->wire->addHookAfter("ProcessLogin::loginFormProcessReady", $this, "fillLoginForm");
        $this->wire->addHookAfter("Session::authenticate", $this, "overridePasswordAuthentication");
    }


    protected function fillLoginForm(HookEvent $event)
    {
        $uname = $this->wire->input->post->login_name ?? '';
        $upass = $this->wire->input->post->login_pass ?? '';
        if ('' === $uname && '' === $upass) {
            $this->wire->input->post->login_name = $this->superuser->name;
            $this->wire->input->post->login_pass = 'dummy_password'; // Not empty so PW to processes it
        }
    }


    protected function overridePasswordAuthentication(HookEvent $event)
    {
        $user = $event->arguments(0);
        if ($user->id === $this->superuser->id && 'TfaWebAuthn' === $user->hasTfa()) {
            // Override earlier failed password authentication for this user
            // WebAuthn 2FA will kick in now, just as if the password was entered correctly.
            // So we only need to press our YubiKey button or use our face/fingerprint etc.
            $event->return = true;
        }
    }
}
