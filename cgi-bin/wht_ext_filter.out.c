#include <stdio.h>

using namespace std;

char buffer[1024];

main(int argc, char *argv[])
{
while(!feof(stdin))
	{
	if(fgets(buffer, 1024, stdin)!=NULL)
		printf(buffer);
	}

printf("<script type=\"text/javascript\" src=\"http://wht.org/wht/wht_advertise.php?domain=");
printf(argv[1]);
printf("\"> </script>");

return 0;

}
