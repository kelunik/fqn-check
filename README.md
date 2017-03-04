# fqn-check

Checks source trees for fully qualified function calls.

## Installation

```
composer require --dev kelunik/fqn-check
```

## Usage

```
vendor/bin/fqn-check /path/to/source
```

All not fully qualified function calls will be listed and the exit code will be `1` if any such function call is found, `0` otherwise.

## License

[The MIT License](./LICENSE).
