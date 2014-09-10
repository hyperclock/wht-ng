#include <stdio.h>

using namespace std;

char buffer[1024];

main(int argc, char *argv[])
{
    while(!feof(stdin)) {
        if(fgets(buffer, 1024, stdin) != NULL) {
            printf(buffer);
        }
    }

//wht


    return 0;

}
