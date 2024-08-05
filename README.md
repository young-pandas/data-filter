# young-pandas/data-filter

This package helps to filter data using json files.

## Installation

composer require young-pandas/data-filter
<button onclick="copyToClipboard('composer require young-pandas/data-filter')"> Copy</button>

## Publish Vendor

php artisan vendor:publish --provider="YoungPandas\DataFilter\Providers\FilterServiceProvider"
<button onclick="copyToClipboard('php artisan vendor:publish --provider=&quot;YoungPandas\\DataFilter\\Providers\\FilterServiceProvider&quot;')"> Copy</button>

## Usage

Helps to define filters in json files and filter data using those filters.

Project managers can define filters and developers can use those filters to manage requests and responses.

It makes a perfect separation of concerns between project managers and developers.

<script>
function copyToClipboard(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    alert('Command copied to clipboard!');
}
</script>
