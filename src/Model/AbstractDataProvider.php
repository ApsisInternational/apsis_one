<?php

namespace Apsis\One\Model;

// TODO: common values shared between entities
abstract class AbstractDataProvider extends AbstractData
{
    protected function getCustomerId()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getSubscriberId()
    {
        //return $this->object->getSomeValue();
        return '';
    }

    protected function getShopId()
    {
        //return $this->object->getSomeValue();
        return $this->object->shopId;
    }

    protected function getShopGroupId()
    {
        //return $this->object->getSomeValue();
        return $this->object->shopGroupId;
    }

    protected function getShopName()
    {
        //return $this->object->getSomeValue();
        return $this->object->shopName;
    }

    protected function getShopGroupName()
    {
        //return $this->object->getSomeValue();
        return $this->object->shopGroupName;
    }

    protected function getIsGuest()
    {
        //return $this->object->getSomeValue();
        return '';
    }
}