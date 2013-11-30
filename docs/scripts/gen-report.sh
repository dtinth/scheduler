#!/bin/bash

set -e

rm -rf gen
mkdir -p gen

cat report.md \
  | ruby scripts/markdown-to-html.rb \
  | pandoc -f html -t latex \
  | ruby scripts/postprocess-tex.rb \
  > gen/report.tex

cd gen

pdflatex report.tex
pdflatex report.tex


