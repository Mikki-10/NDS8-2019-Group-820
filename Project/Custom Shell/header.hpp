//
// Custom Shell [ Pierre & Andrej ]
//

#ifndef NDS_SHELL_HEADER_H
#define NDS_SHELL_HEADER_H

#include <iostream>
#include <vector>
#include <functional>
#include <utility>
#include <memory>
#include <map>

typedef std::string String;

/**
 * Command type:
 *      accepts any type (in C/C++ it's mostly void pointer)
 *      returns boolean, whether the shell should continue
 */
typedef std::function<bool(void *)> Command;

#define out(in) std::cout << in << std::endl
#define str(input) std::to_string(input)


//#include <stdio.h>
//#include <stdlib.h>
//
//#include <unistd.h>
//#include <string.h>
//
//#include <sys/time.h>
//#include <sys/wait.h>
//
//
//#ifdef __linux__
//    #include <ldap.h>  // https://linux.die.net/man/3/ldap
//#else
//    #include <w32api/winldap.h>
//#endif

#endif //NDS_SHELL_HEADER_H
