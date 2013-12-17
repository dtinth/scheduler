#!/bin/bash

set -e

rm -rf gen/out
mkdir -p gen/out
mkdir -p gen/contents

for I in contents/*
do
  J=gen/contents/`basename "$I" .tex`.tex
  if [ "$I" -nt "$J" ]
  then
    ruby scripts/process-section.rb "$I" "$J"
  fi
done

for I in example-data/*
do
  J=gen/contents/ex-`basename "$I" .csv`.tex
  if [ "$I" -nt "$J" -o scripts/process-data.rb -nt "$J" ]
  then
    ruby scripts/process-data.rb "$I" "$J"
  fi
done

cp template.tex gen/out/report.tex
cd gen/out
pdflatex report.tex
pdflatex report.tex

