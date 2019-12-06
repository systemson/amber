<?php

namespace Amber\Controller;

use Amber\Model\Provider\AbstractProvider as Provider;
use Psr\Http\Message\ServerRequestInterface as Request;
use Amber\Helpers\Caster\Caster;
use Amber\Container\Facades\Str;

trait ListableTrait
{
    protected function getWhereAll(
        iterable $queryString,
        Provider $provider
    ): iterable {
        if (empty($queryString)) {
            return [];
        }

        foreach ($queryString as $key => $value) {
            $attr = $provider->new()->getAttribute($key);

            if (is_array($value)) {
                foreach ($value as $value) {
                    $realValue[] = (new Caster())->cast($value, $attr->getType());
                }
            } else {
                $realValue = (new Caster())->cast($value, $attr->getType());
            }

            $queryString->set($key, $realValue);
        }

        return $queryString;
    }

    protected function getOrderBy(string $orderBy = null)
    {
        return Str::new($orderBy)
            ->explode(':')
        ;
    }

    protected function getResourceList(Provider $provider, Request $request)
    {
        $query = $request->query;

        $whereAll = $this->getWhereAll(
            $query->only($provider->getAttributesNames()),
            $provider
        );

        list($orderCol, $orderSeq) = $this->getOrderBy($query->get('orderBy') ?? $provider->getId());

        $cols = $provider->getAttributesNames();
        $page = $query->get('page');
        $limit = $query->get('limit') ?? 10;
        $page = $query->get('page') ?? 1;

        $dataQuery = $provider->query()
            ->select($cols)
            ->from($provider->getName())
            ->whereAll($whereAll)
            ->setPaging($limit)
            ->page($page)
            ->orderBy($orderCol, $orderSeq ?? '')
        ;

        $countQuery = $provider->query()
            ->select()
            ->from($provider->getName())
            ->whereAll($whereAll)
            ->count()
        ;

        //$count = $provider->first($countQuery);

        /*$count = $provider
            ->count()
            ->whereAll($whereAll)
            //->get()
        ;

        $pagination = [
            'total' => $total = dd($count->getStatement()),
            'current' => $page,
            'last' => ceil($total / $limit),
        ];*/

        return [
            'data' => $provider->get($dataQuery),
            //'pagination' => $pagination
        ];
    }
}
