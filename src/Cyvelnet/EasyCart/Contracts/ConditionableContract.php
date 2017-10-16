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
     *
     * @return bool
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
     * remove all conditions.
     */
    abstract public function removeAllConditions();

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
     * get applied condition without a type.
     *
     * @param $type
     *
     * @return mixed
     */
    public function getConditionsWithoutType($type)
    {
        return $this->getConditions()->filter(function ($condition) use ($type) {
            return $condition->getType() !== $type;
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
     * get conditions which is not targeted on products and is not tax type condition.
     *
     * @return mixed
     */
    public function getNonProductAndTaxConditions()
    {
        return $this->getConditions()->filter(function (CartCondition $condition) {
            return $condition->getTarget() !== 'products' && $condition->getType() !== 'tax';
        });
    }

    /**
     * get tax type condition.
     *
     * @return mixed
     */
    public function getTaxConditions()
    {
        return $this->getConditions()->filter(function (CartCondition $condition) {
            return $condition->getType() === 'tax';
        });
    }

    /**
     * calculate condition values.
     *
     * @return int|float
     */
    protected function calculateTotal()
    {
        $sum = $this->subtotal();

        $this->getCalculateableCondition()->each(function (CartCondition $condition) use (&$sum) {
            $sum += $this->calculateValue($condition->getValue(), $sum, $condition->maxValue());
        });

        // calculate tax after all conditions
        return $this->calculateTaxes($sum);
    }

    protected function calculateValue($value, $baseValue, $maxValue = null)
    {
        if (preg_match('/[+-]?[0-9.]+%/', preg_replace('/\s+/', '', $value), $matches)) {
            $conditionValue = (float) $matches[0];
            $percentage = ((float) $matches[0]) / 100;

            $value = $baseValue * $percentage;
        } else {
            $conditionValue = (float) $value;
            $value = (float) $value;
        }

        $calculatedValue = abs(($maxValue && abs($value) > $maxValue) ? $maxValue : $value);

        return $conditionValue >= 0 ? $calculatedValue : -$calculatedValue;
    }

    /**
     * @param $sum
     *
     * @return mixed
     */
    protected function calculateTaxes($sum)
    {
        $total = $sum;

        $this->getTaxConditions()->each(function (CartCondition $condition) use (&$total) {
            $total += $this->calculateValue($condition->getValue(), $total);
        });

        return $total;
    }
}
