<?php

  // Pour mocker la date actuelle, si nécessaire
  define("ENV", "DEV");
  if (ENV === "DEV") define("MOCK_NOW", "2021-10-25");