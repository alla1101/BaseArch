<?php

namespace BaseTools\ComplexSearch;

use Illuminate\Http\Request;

trait injectionTrait
{
    private function discardRequestIfNotFound($injectedArray)
    {
        $arr = json_decode($injectedArray, true);
        $newarr = $this->recursiveReqDiscard($arr);

        return json_encode($newarr);
    }

    private function recursiveReqDiscard($arr)
    {
        foreach ($arr as $key => $value) {
            if ($this->checkForRequest($key)) {
                unset($arr[$key]);
            }
            if (is_array($value)) {
                $arr[$key] = $this->recursiveReqDiscard($value);
                continue;
            }
            if ($this->checkForRequest($value)) {
                unset($arr[$key]);
            }
        }
        return $arr;
    }

    private function checkForRequest($val)
    {
        preg_match_all("/^Request[.\w+]+/", "" . $val, $matches, PREG_SET_ORDER, 0);
        if (count($matches)) {
            return true;
        }
        return false;
    }

    private function injectRequestParam($request, $injectedArray)
    {
        preg_match_all('"Request[.\w+]+"', $injectedArray, $matches, PREG_SET_ORDER, 0);
        if (count($matches)) {

            foreach ($matches as $match) {
                // Auth Query Creator

                $injectedArray = $this->requestCreator($request, $match[0], $injectedArray);
            }
        }

        return $injectedArray;
    }

    private function requestCreator($request, $match, $injectedArray)
    {
        $param = str_replace("Request.", "", $match);
        $input = $request->all();
        $value = isset($input[$param]) ? $input[$param] : $match;

        if (!is_numeric($value)) {
            $injectedArrayx = preg_replace("/Request." . $param . "/", $value, $injectedArray);
        } else {
            $injectedArrayx = preg_replace("/\"Request." . $param . "\"/", $value, $injectedArray);
        }

        if (!is_null($injectedArrayx)) {
            $injectedArray = $injectedArrayx;
        }

        return $injectedArray;
    }

    private function injectAuthParam($request, $injectedArray)
    {

        preg_match_all('"Auth[.\w+]+"', $injectedArray, $matches, PREG_SET_ORDER, 0);

        if (count($matches)) {

            foreach ($matches as $match) {
                // Auth Query Creator
                $injectedArray = $this->queryCreator($match[0], $injectedArray);
            }
        }

        return $injectedArray;
    }

    private function queryCreator($match, $injectedArray)
    {
        $param = str_replace("Auth.", "", $match);

        $arr = explode(".", $param);
        $query = auth()->user();
        foreach ($arr as $value) {
            $query = $query->{$value};
        }
        $value = $query;
        if (!is_numeric($value)) {
            $injectedArrayx = preg_replace("/Auth." . $param . "/", $value, $injectedArray);
        } else {
            $injectedArrayx = preg_replace("/\"Auth." . $param . "\"/", $value, $injectedArray);
        }

        if (!is_null($injectedArrayx)) {
            $injectedArray = $injectedArrayx;
        }

        return $injectedArray;
    }

    private function injectRouteParameters($request, $injectedArray)
    {
        preg_match_all('"Parameter[.\w+]+"', $injectedArray, $matches, PREG_SET_ORDER, 0);
        foreach ($matches as $match) {
            $param = str_replace("Parameter.", "", $match[0]);
            $value = $request->route()->parameter($param);

            if (!is_numeric($value)) {
                $injectedArrayx = preg_replace("/Parameter." . $param . "/", $value, $injectedArray);
            } else {
                $injectedArrayx = preg_replace("/\"Parameter." . $param . "\"/", $value, $injectedArray);
            }

            if (!is_null($injectedArrayx)) {
                $injectedArray = $injectedArrayx;
            }
        }

        return $injectedArray;
    }
}
