<?php

$page_title = 'Страница не найдена';
http_response_code(404);

require 'html/404.html';

exit;