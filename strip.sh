#!/usr/bin/env bash
#strip_classes.sh
# Recursively empties all class="" attributes in .html.twig files under templates/

find templates -type f -name '*.html.twig' | while read -r file; do
  # Use perl for in-place, regex-safe replacement
    perl -pi -e 's/\bclass="[^"]*"/class=""/g' "$file"
    done

    echo "All class attributes have been emptied in templates/**/*.html.twig"

