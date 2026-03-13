<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class LcProxyController extends Controller
{
    public function handle(Request $request, $path = null)
    {
        $targetBase = 'http://142.34.167.125:8080/lc';
        $targetUrl = rtrim($targetBase, '/');

        if (!empty($path)) {
            $targetUrl .= '/' . ltrim($path, '/');
        }

        if ($request->getQueryString()) {
            $targetUrl .= '?' . $request->getQueryString();
        }

        $headers = collect($request->headers->all())
            ->map(fn ($value) => is_array($value) ? implode('; ', $value) : $value)
            ->except([
                'host',
                'cookie',
                'content-length',
                'x-forwarded-for',
                'x-forwarded-proto',
                'x-forwarded-host',
            ])
            ->toArray();

        $client = Http::withHeaders($headers)
            ->withBody($request->getContent(), $request->header('Content-Type', 'application/octet-stream'))
            ->timeout(120)
            ->withoutVerifying();

        $method = strtoupper($request->method());

        $response = $client->send($method, $targetUrl);

        $excludedHeaders = [
            'transfer-encoding',
            'content-encoding',
            'connection',
            'keep-alive',
        ];

        $laravelResponse = response($response->body(), $response->status());


        foreach ($response->headers() as $name => $values) {
            if (in_array(strtolower($name), $excludedHeaders, true)) {
                continue;
            }

            foreach ($values as $value) {
                $laravelResponse->headers->set($name, $value, false);
            }
        }

        return $laravelResponse;
    }
}

