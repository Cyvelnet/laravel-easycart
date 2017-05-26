<?php


class CartItemEventTest extends EasyCartTestCase
{
    /**
     * @test
     */
    public function it_should_fire_event_when_condition_add()
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

        $new50DiscountCondition = new \Cyvelnet\EasyCart\CartCondition('foo condition', '-5');
        $new50DiscountCondition->onProduct([1]);

        $cart->condition($new50DiscountCondition);

        Event::assertDispatched('cart_item.condition.adding');
        Event::assertDispatched('cart_item.condition.added');
    }

    /**
     * @test
     */
    public function it_should_not_fire_add_event_on_condition_not_targeted_on_products()
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

        $new50DiscountCondition = new \Cyvelnet\EasyCart\CartCondition('foo condition', '-5');

        $cart->condition($new50DiscountCondition);

        Event::assertNotDispatched('cart_item.condition.adding');
        Event::assertNotDispatched('cart_item.condition.added');
    }

    /**
     * @test
     */
    public function it_should_fire_update_event_when_add_item()
    {
        Event::fake();
        $cart = $this->getCartInstance();

        $items = $cart->add(
            [
                [
                    'id'    => 1,
                    'name'  => 'foo',
                    'qty'   => 2,
                    'price' => 100,
                ],
            ]
        );

        $cart->update($items->first()->getRowId(), ['qty' => 12]);

        Event::assertDispatched('cart_item.updating');
        Event::assertDispatched('cart_item.updated');
    }
}
