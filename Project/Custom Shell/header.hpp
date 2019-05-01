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


/** Please don't hate me */
typedef std::string String;
typedef std::vector<std::string> StringVec;

/** accepts any type (void*); returns bool, whether the shell should continue */
typedef std::function<bool(StringVec)> Command;

enum Retval {
    SUCCESS = 1,
    FAILURE = 0,
    NOT_FOUND = -1
};


#define out(in) std::cout << in << std::endl
#define unused(...) (void)(__VA_ARGS__)
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

#ifdef __linux__
    #include <ldap.h>  // https://linux.die.net/man/3/ldap
#else
    #include <w32api/winldap.h>
#endif

#endif //NDS_SHELL_HEADER_H
