# remove awkward characters from Bionumbers data

# Copyright (C) 2014 Systems & Signals Research Group, Imperial College London
# Released as free software under GNU GPL 3.0 http://www.gnu.org/licenses/ ; see readme.txt
# Please cite the accompanying paper [see readme.txt] if using this code or this tool in a publication. 

sed -i "s/'/\&apos;/g" bionum1.txt
sed -i "s/µ/\&mu;/g" bionum1.txt
sed -i "s/â€“/\-/g" bionum1.txt
sed -i "s/×/x/g" bionum1.txt
sed -i "s/Ï€/\&pi;/g" bionum1.txt
sed -i "s/±/+-/g" bionum1.txt
sed -i "s/°/\&deg;/g" bionum1.txt
sed -i "s/Ñ…/x/g" bionum1.txt
sed -i "s/Î¼/\&mu;/g" bionum1.txt
sed -i "s/Å/A\&deg;/g" bionum1.txt
sed -i "s/·/\./g" bionum1.txt
sed -i "s/Ëš/\&deg;/g" bionum1.txt
sed -i "s/â‰ˆ/~/g" bionum1.txt
sed -i "s/Î²/\&beta;/g" bionum1.txt
sed -i "s/âˆ’/\-/g" bionum1.txt
sed -i "s/º/\&deg;/g" bionum1.txt
sed -i "s/Î¥/\&gamma;/g" bionum1.txt #106259
sed -i "s/Î´/\&delta;/g" bionum1.txt #106260
sed -i "s/Ï„Î”/\&tau;\&Delta;/g" bionum1.txt #105994
sed -i "s/â‰¥/\>=/g" bionum1.txt #105771
sed -i "s/â†’/\-\>/g" bionum1.txt #105678
sed -i "s/Î©/\&Omega;/g" bionum1.txt #106119
sed -i "s/Ïƒ/\&sigma;/g" bionum1.txt #105161
sed -i "s/ï¬ﾁ/fi/g" bionum1.txt
sed -i "s/â€™//g" bionum1.txt #101873
sed -i "s/âˆ†GË/\&Delta;G\&deg;/g" bionum1.txt #106508
sed -i "s/ö/o/g" bionum1.txt
sed -i "s/Î³/\&gamma;/g" bionum1.txt #105335
