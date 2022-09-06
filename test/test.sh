#!/bin/bash
COUNT=0
while [ $COUNT -le 1000 ]; do
    echo call : $COUNT
    let COUNT=COUNT+1;
    curl http://tl-dr.in
done;
