<?php
declare(strict_types=1);

namespace Genkgo\Mail\Transport;

use Genkgo\Mail\AddressList;
use Genkgo\Mail\Header\Sender;
use Genkgo\Mail\MessageInterface;
use Genkgo\Mail\TransportInterface;

final class InjectSenderHeaderTransport implements TransportInterface
{
    /**
     * @var TransportInterface
     */
    private $decoratedTransport;

    /**
     * @param TransportInterface $transport
     */
    public function __construct(TransportInterface $transport)
    {
        $this->decoratedTransport = $transport;
    }

    /**
     * @param MessageInterface $message
     * @return void
     */
    public function send(MessageInterface $message): void
    {
        $fromHeaders = $message->getHeader('from');
        if (!\is_array($fromHeaders)) {
            $fromHeaders = \iterator_to_array($fromHeaders);
        }

        if (isset($fromHeaders[0])) {
            $addressList = AddressList::fromString(
                $fromHeaders[0]->getValue()->getRaw()
            );

            if ($addressList->count() > 1) {
                $message = $message
                    ->withHeader(
                        new Sender(
                            $addressList->first()
                        )
                    );
            }
        }

        $this->decoratedTransport->send($message);
    }
}
