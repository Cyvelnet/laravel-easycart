<?php

/**
 * Class CartTest.
 */
class CartTest extends EasyCartTestCase
{
    /**
     * @test
     */
    public function it_should_has_a_default_cart_instance()
    {
        $cart = $this->getCartInstance();

        $this->assertEquals('default', $cart->getCurrentInstanceName());
    }

    /**
     * @test
     */
    public function it_should_allowed_to_have_multiple_instances()
    {
        $cart = $this->getCartInstance();
        $cart->add(1, 'foo', 10.99, 2);
        $cart->add(2, 'foobar', 20.99, 4);

        $cart->instance('fooInstance')->add(1, 'foo', 30.99, 6);

        $this->assertEquals(2, $cart->instance()->content()->count());
        $this->assertEquals(1, $cart->instance('fooInstance')->content()->count());
    }

    /**
     * @test
     */
    public function it_should_indicate_a_cart_is_expired()
    {
        $cart = $this->getCartInstance();
        $expiration = (new DateTime())->sub(new DateInterval('PT1M'));
        // the time we create this object, its already expired.
        $cart->instance('fooInstance', $expiration)->add(1, 'foo', 30.99, 6);

        $this->assertNotNull($cart->expirationTimestamp());
        $this->assertTrue($cart->isExpired());
    }

    /**
     * @test
     */
    public function it_should_able_to_provides_proper_expiration_states()
    {
        $cart = $this->getCartInstance();
        $expiration = (new DateTime())->add(new DateInterval('PT1H'));

        $cart->instance('fooInstance', $expiration)->add(1, 'foo', 30.99, 6);

        $this->assertNotNull($cart->expirationTimestamp());
        $this->assertFalse($cart->isExpired());
    }

    /**
     * @test
     */
    public function it_should_always_valid_when_not_specify_expiration()
    {
        $cart = $this->getCartInstance();

        $cart->instance('fooInstance')->add(1, 'foo', 30.99, 6);

        $this->assertFalse($cart->isExpired());
    }

    /**
     * @test
     */
    public function it_should_accept_carbon_or_datetime_instances()
    {
        $cart = $this->getCartInstance();
        $expiration = (new \Carbon\Carbon())->addMinute(15);
        // the time we create this object, its already expired.
        $cart->instance('fooInstance', $expiration)->add(1, 'foo', 30.99, 6);

        $this->assertNotNull($cart->expirationTimestamp());
        $this->assertFalse($cart->isExpired());
    }

    /**
     * @test
     */
    public function it_can_add_an_item_and_persisted()
    {
        $cart = $this->getCartInstance();
        $cart->add(1, 'foo', 10.99, 2);

        $this->assertEquals(2, $cart->qty());
    }

    /**
     * @test
     */
    public function it_can_add_multiple_items()
    {
        $cart = $this->getCartInstance();
        $cart->add([
            [
                'id'    => 1,
                'name'  => 'foo',
                'qty'   => 2,
                'price' => 10.99,
            ],
            [
                'id'    => 2,
                'name'  => 'foobar',
                'qty'   => 4,
                'price' => 20.99,
            ],
        ]);

        $this->assertEquals(6, $cart->qty());
    }

    /**
     * @test
     */
    public function it_can_add_item_with_weight_or_without_weight()
    {
        $cart = $this->getCartInstance();
        $cart->add([
            [
                'id'    => 1,
                'name'  => 'foo',
                'qty'   => 2,
                'price' => 10.99,
            ],
            [
                'id'     => 2,
                'name'   => 'foobar',
                'qty'    => 4,
                'price'  => 20.99,
                'weight' => 5.0,

            ],
        ]);

        $cart->add(3, 'foobarbaz', 30.99, 2, []);
        $cart->add(4, 'foobarbazfoob', 30.99, 6, [], 0.5);

        $this->assertEquals(23.0, $cart->weight());
    }

    /**
     * @test
     */
    public function it_should_not_perform_a_cross_instance_cart_destroy()
    {
        $cart = $this->getCartInstance();
        $cart->add(1, 'foo', 10, 2);
        $cart->add(2, 'foobar', 20, 4);

        $cart->instance('fooInstance')->add(1, 'foo', 10, 2);

        $cart->instance()->destroy();

        $this->assertEquals(0, $cart->instance()->content()->count());
        $this->assertEquals(1, $cart->instance('fooInstance')->content()->count());
    }

    /**
     * @test
     */
    public function it_should_add_qty_on_duplicated_item()
    {
        $cart = $this->getCartInstance();

        $cart->add(1, 'foo', 10, 2, ['color' => 'red', 'size' => 'M']);
        $cart->add(1, 'foo', 10, 4, ['color' => 'red', 'size' => 'M']);

        $this->assertEquals(6, $cart->qty());
    }

    /**
     * @test
     */
    public function it_should_add_qty_on_duplicated_item_even_with_multiple_add_at_once()
    {
        $cart = $this->getCartInstance();

        $cart->add(
            [
                [
                    'id'    => 1,
                    'name'  => 'foo',
                    'qty'   => 2,
                    'price' => 10.99,
                ],
                [
                    'id'    => 1,
                    'name'  => 'foo',
                    'qty'   => 4,
                    'price' => 10.99,
                ],
            ]
        );

        $this->assertEquals(6, $cart->qty());
    }

    /**
     * @test
     */
    public function it_should_get_an_accurate_cart_total()
    {
        $cart = $this->getCartInstance();

        $cart->add(
            [
                [
                    'id'    => 1,
                    'name'  => 'foo',
                    'qty'   => 2,
                    'price' => 10.99,
                ],
                [
                    'id'    => 1,
                    'name'  => 'foo',
                    'qty'   => 4,
                    'price' => 10.99,
                ],
            ]
        );

        $this->assertEquals((10.99 * 6), $cart->subtotal());
    }

    /**
     * @test
     */
    public function it_should_not_be_expireable_when_not_provides_expiration_data()
    {
        $cart = $this->getCartInstance();

        $cart->add(1, 'foo', 10.99, 2);

        $this->assertNull($cart->expirationTimestamp());
    }

    /**
     * @test
     */
    public function it_should_has_expiration_information_when_expiration_data_is_provided()
    {
        $cart = $this->getCartInstance();

        $expiredAt = (new DateTime())->add(new DateInterval('PT5M'));
        $timestamp = $expiredAt->getTimestamp();

        $cart->instance('fooInstance', $expiredAt)->add(1, 'foo', 10.99, 2);

        $this->assertEquals($timestamp, $cart->expirationTimestamp());
    }

    /**
     * @test
     */
    public function it_should_calculate_a_percentage_condition_correctly()
    {
        $cart = $this->getCartInstance();
        $cart->add(
            [
                [
                    'id'    => 1,
                    'name'  => 'foo',
                    'qty'   => 2,
                    'price' => 100,
                ],
                [
                    'id'    => 2,
                    'name'  => 'foobar',
                    'qty'   => 4,
                    'price' => 200,
                ],
            ]
        );

        $new50PercentDiscountCondition = new \Cyvelnet\EasyCart\CartCondition('50% Off', '-50%');

        $cart->condition($new50PercentDiscountCondition);

        $this->assertEquals(500, $cart->total());
    }

    /**
     * @test
     */
    public function it_should_calculate_a_percentage_condition_with_a_maximum_discount_value_correctly()
    {
        $cart = $this->getCartInstance();
        $cart->add(
            [
                [
                    'id'    => 1,
                    'name'  => 'foo',
                    'qty'   => 2,
                    'price' => 100,
                ],
                [
                    'id'    => 1,
                    'name'  => 'foo',
                    'qty'   => 4,
                    'price' => 200,
                ],
            ]
        );

        $new50PercentDiscountCondition = new \Cyvelnet\EasyCart\CartCondition('50% Off', '-50%');
        $new50PercentDiscountCondition->maxAt(80);

        $cart->condition($new50PercentDiscountCondition);

        $this->assertEquals($cart->subtotal() - 80, $cart->total());
    }

    /**
     * @test
     */
    public function it_should_calculate_a_fixed_value_condition_correctly()
    {
        $cart = $this->getCartInstance();
        $cart->add(
            [
                [
                    'id'    => 1,
                    'name'  => 'foo',
                    'qty'   => 2,
                    'price' => 100,
                ],
                [
                    'id'    => 2,
                    'name'  => 'foobar',
                    'qty'   => 1,
                    'price' => 200,
                ],
            ]
        );

        $new50DiscountCondition = new \Cyvelnet\EasyCart\CartCondition('$50 Off', '-50');

        $cart->condition($new50DiscountCondition);

        $this->assertEquals(350, $cart->total());
    }

    /**
     * @test
     */
    public function it_should_apply_condition_to_new_product_added_after_condition_was_added()
    {
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

        $new50DiscountCondition = new \Cyvelnet\EasyCart\CartCondition('$50 Off', '-50');
        $new50DiscountCondition->onProduct([1, 2]);

        $cart->condition($new50DiscountCondition);

        $cart->add(
            [
                [
                    'id'    => 2,
                    'name'  => 'foobar',
                    'qty'   => 2,
                    'price' => 100,
                ],
                [
                    'id'    => 3,
                    'name'  => 'foobaz',
                    'qty'   => 2,
                    'price' => 100,
                ],
            ]
        );

        $this->assertTrue($cart->find(2)->getConditions()->hasCondition($new50DiscountCondition));
        $this->assertTrue($cart->find(3)->getConditions()->doesntHaveCondition($new50DiscountCondition));
    }

    public function it_should_apply_condition_to_all_products_when_no_specified_which_to_apply()
    {
        $cart = $this->getCartInstance();
        $cart->add(
            [
                [
                    'id'    => 1,
                    'name'  => 'foo',
                    'qty'   => 2,
                    'price' => 100,
                ],
                [
                    'id'    => 2,
                    'name'  => 'foobar',
                    'qty'   => 2,
                    'price' => 100,
                ],
            ]
        );

        $new50DiscountCondition = new \Cyvelnet\EasyCart\CartCondition('$50 Off', '-50');
        $new50DiscountCondition->onProduct();

        $cart->condition($new50DiscountCondition);

        $this->assertTrue($cart->find(1)->getConditions()->hasCondition($new50DiscountCondition));
        $this->assertTrue($cart->find(2)->getConditions()->hasCondition($new50DiscountCondition));
    }

    /**
     * @test
     */
    public function it_should_calculate_product_condition_correctly()
    {
        $cart = $this->getCartInstance();
        $cart->add(
            [
                [
                    'id'    => 1,
                    'name'  => 'foo',
                    'qty'   => 2,
                    'price' => 100,
                ],
                [
                    'id'    => 2,
                    'name'  => 'foobar',
                    'qty'   => 2,
                    'price' => 400,
                ],
                [
                    'id'    => 3,
                    'name'  => 'foobarbaz',
                    'qty'   => 2,
                    'price' => 800,
                ],
            ]
        );

        $new50DiscountCondition = new \Cyvelnet\EasyCart\CartCondition('$50 Off', '-50%');
        $new50DiscountCondition->onProduct([1, 2]);

        // $add20ToSubtotalCondition = new \Cyvelnet\EasyCart\CartCondition('Add $20', '20');

        // $cart->condition([$new50DiscountCondition, $add20ToSubtotalCondition]);
        $cart->condition($new50DiscountCondition);

        //$this->assertTrue($cart->find(1)->getConditions()->hasCondition($new50DiscountCondition));
        //$this->assertTrue($cart->find(2)->getConditions()->hasCondition($new50DiscountCondition));

        $this->assertEquals(2100, $cart->total());
    }

    /**
     * @test
     */
    public function it_should_delete_the_condition_by_name()
    {
        $cart = $this->getCartInstance();
        $cart->add(
            [
                [
                    'id'    => 1,
                    'name'  => 'foo',
                    'qty'   => 2,
                    'price' => 100,
                ],
                [
                    'id'    => 2,
                    'name'  => 'foobar',
                    'qty'   => 2,
                    'price' => 100,
                ],
            ]
        );

        $new50DiscountCondition = new \Cyvelnet\EasyCart\CartCondition('$50 Off', '-50');
        $new50DiscountCondition->onProduct();

        $cart->condition($new50DiscountCondition);

        $cart->removeConditionByName('$50 Off');

        $this->assertTrue($cart->getConditions()->doesntHaveCondition($new50DiscountCondition));
        $this->assertTrue($cart->find(1)->getConditions()->doesntHaveCondition($new50DiscountCondition));
        $this->assertTrue($cart->find(2)->getConditions()->doesntHaveCondition($new50DiscountCondition));
    }

    /**
     * @test
     */
    public function it_should_delete_all_conditions()
    {
        $cart = $this->getCartInstance();
        $cart->add(
            [
                [
                    'id'    => 1,
                    'name'  => 'foo',
                    'qty'   => 2,
                    'price' => 100,
                ],
                [
                    'id'    => 2,
                    'name'  => 'foobar',
                    'qty'   => 2,
                    'price' => 100,
                ],
            ]
        );

        $new50DiscountCondition = new \Cyvelnet\EasyCart\CartCondition('$50 Off', '-50', 'discount');
        $new50DiscountCondition->onProduct();

        $newDiscountCondition = new \Cyvelnet\EasyCart\CartCondition('$5 Off', '-5', 'discount');
        $newDiscountCondition->onProduct();

        $cart->condition([$new50DiscountCondition, $newDiscountCondition]);

        $cart->removeAllConditions();

        $this->assertTrue($cart->getConditions()->doesntHaveCondition($new50DiscountCondition));
        $this->assertTrue($cart->getConditions()->doesntHaveCondition($newDiscountCondition));
        $this->assertTrue($cart->find(1)->getConditions()->doesntHaveCondition($new50DiscountCondition));
        $this->assertTrue($cart->find(1)->getConditions()->doesntHaveCondition($newDiscountCondition));
        $this->assertTrue($cart->find(2)->getConditions()->doesntHaveCondition($new50DiscountCondition));
        $this->assertTrue($cart->find(2)->getConditions()->doesntHaveCondition($newDiscountCondition));
    }

    /**
     * @test
     */
    public function it_should_delete_the_condition_by_type()
    {
        $cart = $this->getCartInstance();
        $cart->add(
            [
                [
                    'id'    => 1,
                    'name'  => 'foo',
                    'qty'   => 2,
                    'price' => 100,
                ],
                [
                    'id'    => 2,
                    'name'  => 'foobar',
                    'qty'   => 2,
                    'price' => 100,
                ],
            ]
        );

        $new50DiscountCondition = new \Cyvelnet\EasyCart\CartCondition('$50 Off', '-50', 'discount');
        $new50DiscountCondition->onProduct();

        $newDiscountCondition = new \Cyvelnet\EasyCart\CartCondition('$5 Off', '-5', 'discount');
        $newDiscountCondition->onProduct();

        $cart->condition([$new50DiscountCondition, $newDiscountCondition]);

        $cart->removeConditionByType('discount');

        $this->assertTrue($cart->getConditions()->doesntHaveCondition($new50DiscountCondition));
        $this->assertTrue($cart->getConditions()->doesntHaveCondition($newDiscountCondition));
        $this->assertTrue($cart->find(1)->getConditions()->doesntHaveCondition($new50DiscountCondition));
        $this->assertTrue($cart->find(1)->getConditions()->doesntHaveCondition($newDiscountCondition));
        $this->assertTrue($cart->find(2)->getConditions()->doesntHaveCondition($new50DiscountCondition));
        $this->assertTrue($cart->find(2)->getConditions()->doesntHaveCondition($newDiscountCondition));
    }

    /**
     * @test
     */
    public function it_should_add_cart_item_attributes_to_collection()
    {
        $cart = $this->getCartInstance();
        $cart->add(
            [
                [
                    'id'         => 1,
                    'name'       => 'foo',
                    'qty'        => 2,
                    'price'      => 100,
                    'attributes' => [
                        'color' => 'red',
                    ],
                ],
                [
                    'id'    => 2,
                    'name'  => 'foobar',
                    'qty'   => 2,
                    'price' => 100,
                ],
            ]
        );

        $this->assertTrue($cart->find(1)->attributes->has('color'));
        $this->assertEquals('red', $cart->find(1)->attributes->get('color'));

        // ensure the data is not set to other cart item

        $this->assertFalse($cart->find(2)->attributes->has('color'));
        $this->assertNotSame('red', $cart->find(2)->attributes->get('color'));
    }

    /**
     * @test
     */
    public function it_should_not_apply_condition_when_minimum_purchase_is_not_met()
    {
        $cart = $this->getCartInstance();
        $cart->add(
            [
                [
                    'id'         => 1,
                    'name'       => 'foo',
                    'qty'        => 2,
                    'price'      => 100,
                    'attributes' => [
                        'color' => 'red',
                    ],
                ],
            ]
        );

        $condition = new \Cyvelnet\EasyCart\CartCondition('100OFF', '-100', 'discount');

        $condition->applyWithMinimum(300);

        $cart->condition($condition);

        $this->assertEquals(200, $cart->total());

        // add another product to fulfill the minimum purchase, which is 300 in total
        $cart->add(1, 'foo', 100, 1, [
            'color' => 'red',
        ]);

        $this->assertEquals(200, $cart->total());
    }

    /**
     * @test
     */
    public function it_should_calculate_minimum_purchase_per_cart()
    {
        $cart = $this->getCartInstance();
        $cart->add(
            [
                [
                    'id'         => 1,
                    'name'       => 'foo',
                    'qty'        => 2,
                    'price'      => 100,
                ],
                [
                    'id'         => 2,
                    'name'       => 'bar',
                    'qty'        => 2,
                    'price'      => 300,
                ],
            ]
        );

        $condition = new \Cyvelnet\EasyCart\CartCondition('100OFF', '-100', 'discount');

        // when setting $each to false, you are asking to apply minimum purchase on cart basic
        $condition->applyWithMinimum(300, $each = false);
        $condition->onProduct([1, 2]);

        $cart->condition($condition);

        $this->assertEquals(700, $cart->total());

        // ensure 100OFF discount condition is not apply
        $this->assertEquals(200, $cart->find(1)->total());

        // add one more quantity to make cart fulfill with minimum purchase condition
        $cart->add(1, 'foo', 100, 1);

        // ensure 100OFF discount condition is apply
        $this->assertEquals(300, $cart->find(1)->total());
        $this->assertEquals(800, $cart->total());
    }

    /**
     * @test
     */
    public function it_should_calculate_minimum_purchase_per_items()
    {
        $cart = $this->getCartInstance();
        $cart->add(
            [
                [
                    'id'         => 1,
                    'name'       => 'foo',
                    'qty'        => 2,
                    'price'      => 100,
                ],
                [
                    'id'         => 2,
                    'name'       => 'bar',
                    'qty'        => 2,
                    'price'      => 300,
                ],
            ]
        );

        $condition = new \Cyvelnet\EasyCart\CartCondition('100OFF', '-100', 'discount');

        $condition->applyWithMinimum(300, $each = true);
        $condition->onProduct([1, 2]);

        $this->assertEquals(800, $cart->total());

        $cart->condition($condition);

        $this->assertEquals(700, $cart->total());

        // ensure 100OFF discount condition is not apply
        $this->assertEquals(200, $cart->find(1)->total());

        // add one more quantity to make cart fulfill with minimum purchase condition
        $cart->add(1, 'foo', 100, 1);

        // ensure 100OFF discount condition is apply
        $this->assertEquals(200, $cart->find(1)->total());
        $this->assertEquals(500, $cart->find(2)->total());
        $this->assertEquals(700, $cart->total());
    }

    /**
     * @test
     */
    public function it_should_persist_cart_condition_on_empty_cart(){

        
        $cart = $this->getCartInstance();

        $condition = new \Cyvelnet\EasyCart\CartCondition('100OFF', '-100', 'discount');
        $cart->condition($condition);

        $this->assertEquals(1, $cart->getConditions()->count());

        $cart->add(1, 'Foo', 100, 5);

        $this->assertEquals(400, $cart->total());


    }
}
