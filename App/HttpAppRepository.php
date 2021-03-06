<?php

/*
 * This file is part of the Apisearch PHP Client.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @author PuntMig Technologies
 */

declare(strict_types=1);

namespace Apisearch\App;

use Apisearch\Http\Http;
use Apisearch\Http\HttpRepositoryWithCredentials;
use Apisearch\Token\Token;
use Apisearch\Token\TokenUUID;

/**
 * Class HttpAppRepository.
 */
class HttpAppRepository extends HttpRepositoryWithCredentials implements AppRepository
{
    /**
     * Add token.
     *
     * @param Token $token
     */
    public function addToken(Token $token)
    {
        $response = $this
            ->httpClient
            ->get(
                '/token',
                'post',
                Http::getQueryValues($this),
                [
                    'token' => json_encode($token->toArray()),
                ]);

        $this->throwTransportableExceptionIfNeeded($response);
    }

    /**
     * Delete token.
     *
     * @param TokenUUID $tokenUUID
     */
    public function deleteToken(TokenUUID $tokenUUID)
    {
        $response = $this
            ->httpClient
            ->get(
                '/token',
                'delete',
                Http::getQueryValues($this),
                [
                    'token' => json_encode($tokenUUID->toArray()),
                ]);

        $this->throwTransportableExceptionIfNeeded($response);
    }
}
