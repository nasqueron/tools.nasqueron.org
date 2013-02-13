<?php
function is_mail ($string) {
	return preg_match('/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z0-9]{2,4}$/', $string);
}

/*
Unit testing ->
1 is_mail("dereckson@gmail.com");
1 is_mail("dereckson@trustspace.42");
1 is_mail("dereckson@gmail.a.a.a.a.a.a.a.ax.org");
1 is_mail("dereckson+wazza@gmail.com");
0 is_mail("dereckson`ls`@gmail.com");
*/