# Flownative ImageOptimizer

Uses a low-level resource publishing target to optimize resources with
binary tools (eg. image optimization).

## Installation

    composer require flownative/image-optimizer

## Usage

See `Configuration/Settings.yaml.example`

Basically you configure this as your main publishing target but with an actual target on top.
The ImageOptimizerTarget simply takes any images published through it checks if the media type
matches one of the configured ones and if so optimizes the image 
(best result effort, so if the filesize after tool is bigger the original is used as optimized).
URLs will always point to the optimized image.

The configuration for a media type contains a `binaryPath` to the tool used for optimization of the 
file and `arguments` which is evaluated as EEL expression with two variables available: 

* `originalPath` - a temporary file path with the original
* `optimizedPath` - a temporary file path to write the optimized file to

The `OptimizationService` can be used on it's own if optimization in another place is required.
It will return an optimized `PersistentResource` object which _may_ have the same binary content as 
the given input stream. 
