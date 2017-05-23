<?php

namespace Cyvelnet\EasyCart\Contracts;

use Cyvelnet\EasyCart\CartCondition;
use Cyvelnet\EasyCart\Collections\CartConditionCollection;

/**
 * Class ConditionableContract.
 */
abstract class ConditionableContract
{
    /**
     * add condition.
     *
     * @param array|\Cyvelnet\EasyCart\CartCondition $condition
     */
    abstract public function condition($condition);

    /**
     * get applied conditions.
     *
     * @return mixed
     */
    abstract public function getConditions();

    /**
     * remove a condition by its type.
     *
     * @param $type
     */
    abstract public function removeConditionByType($type);

    /**
     * remove a condition by its name.
     *
     * @param $name
     */
    abstract public function removeConditionByName($name);

    /**
     * get the conditions that should be calculated.
     *
     * @return mixed
     */
    protected function getCalculateableCondition()
    {
        return new CartConditionCollection();
    }

    /**
     * get applied condition by its type.
     *
     * @param $type
     *
     * @return mixed
     */
    public function getConditionsByType($type)
    {
        return $this->getConditions()->filter(function ($condition) use ($type) {
            return $condition->getType() == $type;
        });
    }

    /**
     * get applied condition by its name.
     *
     * @param $name
     *
     * @return mixed
     */
    public function getConditionsByName($name)
    {
        return $this->getConditions()->filter(function ($condition) use ($name) {
            return $condition->getName() == $name;
        });
    }

    /**
     * get conditions targeted on subtotal.
     *
     * @return mixed
     */
    public function getProductConditions()
    {
        return $this->getConditions()->filter(function (CartCondition $condition) {
            return $condition->getTarget() === 'products';
        });
    }

    /**
     * calculate condition values.
     *
     * @return int|float
     */
    protected function calculateConditionValue()
    {
        $values = [];

        $this->getCalculateableCondition()->each(function (CartCondition $condition) use (&$values) {
            if (preg_match('/[+-]?[0-9]+%/', preg_replace('/\s+/', '', $condition->getValue()), $matches)) {
                $conditionValue = (int) $matches[0];
                $percentage = (float) $matches[0] / 100;

                $value = $this->subtotal() * $percentage;
            } else {
                $conditionValue = (int) $condition->getValue();
                $value = (int) $condition->getValue();
            }

            $calculatedValue = abs(($condition->maxValue() && abs($value) > $condition->maxValue()) ? $condition->maxValue() : $value);

            $values[] = $conditionValue >= 0 ? $calculatedValue : -$calculatedValue;
        });

        return array_sum($values);
    }
}
