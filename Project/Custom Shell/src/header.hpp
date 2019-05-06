//
// Custom Shell [ Pierre & Andrej ]
//

#ifndef NDS_SHELL_HEADER_H
#define NDS_SHELL_HEADER_H

//#define WINDOWS_DEVELOPMENT
//#define DEBUG

#include <iostream>
#include <vector>
#include <functional>
#include <utility>
#include <memory>
#include <map>

/** Please don't hate me for these typedefs */
typedef std::vector<std::string> StringVec;
typedef std::string String;

/** accepts words (vector of strings); returns bool, whether the shell should continue */
typedef std::function<bool(StringVec)> Command;

enum Retval {
    SUCCESS = 1,
    FAILURE = 0,
    NOT_FOUND = -1
};

#define out(in) std::cout << in << std::endl
#define unused(...) (void)(__VA_ARGS__)

#ifdef DEBUG
#define NDS_LDAP_IP_ADDRESS "localhost"
#define debug(in) std::cout << in << std::endl
#else
#define NDS_LDAP_IP_ADDRESS "192.168.32.2"
#define debug(in)
#endif

#endif //NDS_SHELL_HEADER_H
