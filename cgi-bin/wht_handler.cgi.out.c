#include <fstream>

using namespace std;

char buffer[1024];
ifstream file;

main()
{

printf("Content-Type: text/html\n\n");

file.open(getenv("PATH_TRANSLATED"));

if(!file.is_open())
	{
	printf("Can't open file!");
	return 0;
	}
else
	{
	while(!file.eof())
		{
		file.getline(buffer, 1024);
		printf(buffer);
		printf("\n");
		}
	}

printf("<script type=\"text/javascript\" src=\"http://wht.org/wht/wht_advertise.php?domain=");
printf(getenv("PATH_TRANSLATED"));
printf("\"> </script>");
file.close();

return 0;

}
