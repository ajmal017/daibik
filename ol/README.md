## Order of execution of the scripts on daily basis.

   1] fetch_ol_fo_stocks.js
   2] refractor_ol_fo_stocks.js

   Open Low
   ----------
   1] next_match.js
   2] ol_not_come_yet.js
   3] fetch_ol_not_come_yet.js( after stock market opens )
   4] find_ol_today.js( after stock market opens )

   Open High
   ----------
   1] next_match_open_high.js
   2] oh_not_come_yet.js
   3] fetch_oh_not_come_yet.js( after stock market opens )
   4] find_oh_today.js( after stock market opens )

## Points to include

   1] Fourth Candle Green Candle.
   2] First three candle lower top lower bottom and red.
   3] Calculate the percentage of gap up opening.
   4] Calculate the percentage of difference between lower prices of 4th and 3rd day.
   5] If fourth candle is red then check subsequesnt candles for turning it to green.
      Count how many lower top lower bottoms were there previously.


Points need to be checked
-------------------------
1] Nifty Direction(Percentage decrease or increase)
2] Stocks percentage high or low(Percentage decrease or increase)
