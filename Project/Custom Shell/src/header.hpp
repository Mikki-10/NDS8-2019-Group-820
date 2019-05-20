///
/// Custom Shell: the JumpShell
/// Made by Andrej, Pierre and Sebastian
///

#ifndef NDS_SHELL_HEADER_H
#define NDS_SHELL_HEADER_H

//#define WINDOWS_DEVELOPMENT
//#define DEBUG

#include <iostream>
#include <vector>

/** Please don't hate me for these typedefs */
typedef std::vector<std::string> StringVec;
typedef std::string String;


enum Retval {
    SUCCESS = 1,
    FAILURE = 0,
    NOT_FOUND = -1
};


/// Special characters to be handled carefully
enum SpecChar {
    ESC = 0x1b,
    ARR_UP = 0x41,
    ARR_DOWN = 0x42,
    ARR_RIGHT = 0x43,
    ARR_LEFT = 0x44,
    ARROW = 0x5b,
    ENTER = 0x0a,
    BACKSPACE = 0x08,
    DELETE = 0x7F
};


/// sout == Standard OUTput
#define sout(in) std::cout << in << std::endl
#define unused(...) (void)(__VA_ARGS__)

#ifdef DEBUG
#define debug(in) std::cout << in << std::endl
#else
#define debug(in)
#endif


#endif //NDS_SHELL_HEADER_H
