<?php

namespace SocialiteProviders\FeiShu;

use SocialiteProviders\Manager\SocialiteWasCalled;

class FeiShuExtendSocialite
{
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('feishu', Provider::class);
    }
}
