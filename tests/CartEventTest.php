<?php


/**
 * Class CartEventTest.
 */
class CartEventTest extends EasyCartTestCase
{
    /**
     * @test
     */
    public function it_should_fire_add_event_when_add_item()
    {
        Event::fake();
        $cart = $this->getCartInstance();
        $cart->add(
            [
                [
                    'id'    => 1,
                    'name'  => 'foo',
                    'qty'   => 2,
                    'price' => 100,
                ],
            ]
        );

        Event::assertDispatched('cart.adding');
        Event::assertDispatched('cart.added');
    }

    /**
     * @test
     */
    public function it_should_fire_delete_event_when_remove_item()
    {
        Event::fake();
        $cart = $this->getCartInstance();
        $item = $cart->add(1, 'foo', 100, 2);

        $cart->remove($item->getRowId());

        Event::assertDispatched('cart.deleting');
        Event::assertDispatched('cart.deleted');
    }
}
