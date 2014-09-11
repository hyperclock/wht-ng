#include <fstream>

using namespace std;

char buffer[1024];
ifstream file;

main()
{
    printf("Content-Type: text/html\n\n");

    file.open(getenv("PATH_TRANSLATED"));

    if(!file.is_open()) {
        printf("Can't open file!");
        return 0;
    } else {
        while(!file.eof()) {
            file.getline(buffer, 1024);
            printf(buffer);
            printf("\n");
        }
    }

//wht

    file.close();

    return 0;

}
