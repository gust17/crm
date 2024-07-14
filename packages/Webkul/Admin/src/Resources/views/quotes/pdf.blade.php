<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache">
    <title>Cotação</title>
    <link href="https://fonts.googleapis.com/css?family=DejaVu+Sans&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 20px;
        }

        .text-center {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #d3d3d3;
            padding: 5px 10px;
        }

        th {
            background-color: #F4F4F4;
            font-weight: bold;
        }

        td {
            color: #3A3A3A;
            vertical-align: middle;
        }

        .label {
            font-weight: bold;
        }

        .logo {
            height: 70px;
            width: 70px;
        }

        .sale-summary {
            margin-top: 40px;
            text-align: right;
        }

        .sale-summary td {
            padding: 3px 5px;
        }

        .sale-summary .bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
<header>
    <h1 class="text-center">{{ __('admin::app.quotes.quote') }}</h1>
</header>

<section class="quote-summary">
    <p><span class="label">{{ __('admin::app.quotes.quote-id') }} -</span> <span class="value">#{{ $quote->id }}</span></p>
    <p><span class="label">{{ __('admin::app.quotes.quote-date') }} -</span> <span class="value">{{ $quote->created_at->format('d-m-Y') }}</span></p>
    <p><span class="label">{{ __('admin::app.quotes.valid-until') }} -</span> <span class="value">{{ $quote->expired_at->format('d-m-Y') }}</span></p>
    <p><span class="label">Data da Proposta -</span> <span class="value">{{ $quote->getAttribute('dt_proposta') ? $quote->getAttribute('dt_proposta')->format('d-m-Y') : 'Data não disponível' }}</span></p>
    <p><span class="label">{{ __('admin::app.quotes.sales-person') }} -</span> <span class="value">{{ $quote->user->name }}</span></p>
</section>

<table>
    <thead>
    <tr>
        <th>Cliente Imediato</th>
        @if ($quote->shipping_address)
            <th>Cliente Final</th>
        @endif
    </tr>
    </thead>
    <tbody>
    @if ($quote->billing_address)
        <td>
            <p>{{ $quote->billing_address['address'] }}</p>
            <p>{{ $quote->billing_address['postcode'] . ' ' . $quote->billing_address['city'] }}</p>
            <p>{{ $quote->billing_address['state'] }}</p>
            <p>{{ $quote->billing_address['country'] ? core()->country_name($quote->billing_address['country']) : 'N/A' }}</p>
        </td>
    @endif
    @if ($quote->shipping_address)
        <td>
            <p>{{ $quote->shipping_address['address'] }}</p>
            <p>{{ $quote->shipping_address['postcode'] . ' ' . $quote->shipping_address['city'] }}</p>
            <p>{{ $quote->shipping_address['state'] }}</p>
            <p>{{ isset($quote->shipping_address['country']) ? core()->country_name($quote->shipping_address['country']) : 'N/A' }}</p>
        </td>
    @endif
    </tbody>
</table>

<table class="items">
    <thead>
    <tr>
        <th>UND</th>
        <th>{{ __('admin::app.quotes.product-name') }}</th>
        <th class="text-center">{{ __('admin::app.quotes.price') }}</th>
        <th class="text-center">{{ __('admin::app.quotes.quantity') }}</th>
        <th class="text-center">{{ __('admin::app.quotes.amount') }}</th>
        <th class="text-center">{{ __('admin::app.quotes.tax') }}</th>
        <th class="text-center">{{ __('admin::app.quotes.grand-total') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($quote->items as $item)
        <tr>
            <td>{{ $item->sku }}</td>
            <td>{{ $item->name }}</td>
            <td class="text-center">{!! core()->formatBasePrice($item->price, true) !!}</td>
            <td class="text-center">{{ $item->quantity }}</td>
            <td class="text-center">{!! core()->formatBasePrice($item->total, true) !!}</td>
            <td class="text-center">{!! core()->formatBasePrice($item->tax_amount, true) !!}</td>
            <td class="text-center">{!! core()->formatBasePrice($item->total + $item->tax_amount, true) !!}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table class="sale-summary">
    <tr>
        <td>{{ __('admin::app.quotes.sub-total') }}</td>
        <td>-</td>
        <td>{!! core()->formatBasePrice($quote->sub_total, true) !!}</td>
    </tr>
    <tr>
        <td>{{ __('admin::app.quotes.tax') }}</td>
        <td>-</td>
        <td>{!! core()->formatBasePrice($quote->tax_amount, true) !!}</td>
    </tr>
    <tr class="bold">
        <td><strong>{{ __('admin::app.quotes.grand-total') }}</strong></td>
        <td><strong>-</strong></td>
        <td><strong>{!! core()->formatBasePrice($quote->grand_total, true) !!}</strong></td>
    </tr>
</table>
</body>
</html>
