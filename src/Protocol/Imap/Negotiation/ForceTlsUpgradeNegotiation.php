<?php
declare(strict_types=1);

namespace Genkgo\Mail\Protocol\Imap\Negotiation;

use Genkgo\Mail\Exception\ConnectionInsecureException;
use Genkgo\Mail\Protocol\ConnectionInterface;
use Genkgo\Mail\Protocol\Imap\Client;
use Genkgo\Mail\Protocol\Imap\NegotiationInterface;
use Genkgo\Mail\Protocol\Imap\Request\CapabilityCommand;
use Genkgo\Mail\Protocol\Imap\Request\StartTlsCommand;
use Genkgo\Mail\Protocol\Imap\Response\CapabilityResponse;

final class ForceTlsUpgradeNegotiation implements NegotiationInterface
{
    /**
     * @var ConnectionInterface
     */
    private $connection;
    /**
     * @var int
     */
    private $crypto;

    /**
     * ConnectionNegotiation constructor.
     * @param ConnectionInterface $connection
     * @param int $crypto
     */
    public function __construct(
        ConnectionInterface $connection,
        int $crypto
    ) {
        $this->connection = $connection;
        $this->crypto = $crypto;
    }


    /**
     * @param Client $client
     * @throws ConnectionInsecureException
     */
    public function negotiate(Client $client): void
    {
        if (empty($this->connection->getMetaData(['crypto']))) {
            $reply = $client->emit(new CapabilityCommand());
            $reply->assertOk();

            $capabilityResponse = new CapabilityResponse($reply);

            if ($capabilityResponse->isAdvertising('STARTTLS')) {
                $client
                    ->emit(new StartTlsCommand())
                    ->assertTagged();

                $this->connection->upgrade($this->crypto);
            }
        }

        if (empty($this->connection->getMetaData(['crypto']))) {
            throw new ConnectionInsecureException(
                'Server does not support STARTTLS. Use imaps:// or to allow insecure connections use imap-starttls://'
            );
        }
    }
}