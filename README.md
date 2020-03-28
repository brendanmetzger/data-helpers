# Data Helpers

## `XMLresource` trait

- `find` returns a nodelist
- `select` returns a node or null
- `load` loads a filename (note, it validates on parse, so make a doctype)
- `save` saves any changes back to the file loaded.

## `Benchmark` class

- instantiate with a flag for the starting time (defaults to 'start')
- use `split('flag', 'compare')` to mark the distance in time between another event
- use `progress` to draw a cli-worthy progress bar

## `Serial` Class

- use `convert` to turn an integer into a string without numbers, or such a string into an integer; input value remains the same throughout conversions back and forth
- character sets can be applied to convert any integer into any range-able character set.
