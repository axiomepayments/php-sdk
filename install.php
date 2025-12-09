<?php

/**
 * AxiomePayments PHP SDK Installation Script
 *
 * This script helps install and verify the AxiomePayments PHP SDK setup.
 */

echo "AxiomePayments PHP SDK Installation\n";
echo "====================================\n\n";

// Check PHP version
echo "Checking PHP version...\n";
$phpVersion = PHP_VERSION;
$requiredVersion = '8.0.0';

if (version_compare($phpVersion, $requiredVersion, '<')) {
    echo "❌ PHP {$requiredVersion} or higher is required. You have {$phpVersion}\n";
    exit(1);
}

echo "✅ PHP {$phpVersion} is compatible\n\n";

// Check for required extensions
echo "Checking required extensions...\n";
$requiredExtensions = ['json', 'curl'];
$missingExtensions = [];

foreach ($requiredExtensions as $extension) {
    if (!extension_loaded($extension)) {
        $missingExtensions[] = $extension;
        echo "❌ Missing extension: {$extension}\n";
    } else {
        echo "✅ Extension {$extension} is loaded\n";
    }
}

if (!empty($missingExtensions)) {
    echo "\nPlease install the missing extensions and try again.\n";
    exit(1);
}

echo "\n";

// Check if Composer is available
echo "Checking for Composer...\n";
$composerPath = null;

// Try different composer commands
$composerCommands = ['composer', 'composer.phar', './composer.phar'];

foreach ($composerCommands as $command) {
    $output = [];
    $returnCode = 0;
    exec("{$command} --version 2>/dev/null", $output, $returnCode);
    
    if ($returnCode === 0) {
        $composerPath = $command;
        echo "✅ Found Composer: {$command}\n";
        break;
    }
}

if (!$composerPath) {
    echo "❌ Composer not found. Please install Composer first.\n";
    echo "Visit: https://getcomposer.org/download/\n";
    exit(1);
}

echo "\n";

// Install dependencies
echo "Installing dependencies...\n";
$installCommand = "{$composerPath} install --no-dev";
echo "Running: {$installCommand}\n";

$output = [];
$returnCode = 0;
exec($installCommand, $output, $returnCode);

if ($returnCode !== 0) {
    echo "❌ Failed to install dependencies\n";
    echo implode("\n", $output) . "\n";
    exit(1);
}

echo "✅ Dependencies installed successfully\n\n";

// Verify autoloader
echo "Verifying autoloader...\n";
$autoloaderPath = __DIR__ . '/vendor/autoload.php';

if (!file_exists($autoloaderPath)) {
    echo "❌ Autoloader not found at {$autoloaderPath}\n";
    exit(1);
}

require_once $autoloaderPath;

// Test basic SDK functionality
echo "Testing SDK functionality...\n";

try {
    $axiomepayments = new \AxiomePayments\AxiomePayments([
        'api_key' => 'test-key',
        'api_secret' => 'test-secret',
        'environment' => 'sandbox'
    ]);

    echo "✅ SDK can be instantiated\n";
    echo "✅ Payments service is accessible\n";

} catch (Exception $e) {
    echo "❌ SDK test failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n";

// Success message
echo "🎉 Installation completed successfully!\n\n";

echo "Next steps:\n";
echo "1. Get your API credentials from the AxiomePayments merchant dashboard\n";
echo "2. Check the examples/ directory for usage examples\n";
echo "3. Read the README.md for detailed documentation\n\n";

echo "Example usage:\n";
echo "<?php\n";
echo "require_once 'vendor/autoload.php';\n";
echo "\n";
echo "use AxiomePayments\\AxiomePayments;\n";
echo "\n";
echo "\$axiomepayments = new AxiomePayments([\n";
echo "    'api_key' => 'your-api-key',\n";
echo "    'api_secret' => 'your-api-secret',\n";
echo "    'environment' => 'sandbox'\n";
echo "]);\n";
echo "\n";
echo "\$payment = \$axiomepayments->payments->create([\n";
echo "    'amount' => 99.99,\n";
echo "    'currency' => 'USD'\n";
echo "]);\n";
echo "\n";
echo "echo \$payment->payment_url;\n";
echo "?>\n";

echo "\nFor support, visit: https://axiomepayments.com/api-documentation\n"; 