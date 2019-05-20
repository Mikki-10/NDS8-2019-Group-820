///
/// Custom Shell: the JumpShell
/// Made by Andrej, Pierre and Sebastian
///

#ifndef NDS_TERMINAL_H
#define NDS_TERMINAL_H

#include "../header.hpp"
#include <termios.h>    // struct termios, tcgetattr(), tcsetattr()
#include <stdio.h>      // perror(), stderr, stdin, fileno()


/// Terminal Mode
enum TMode {
    SINGLECHAR = 0, /// using termios.h
    STANDARD = 1    /// using std::cin
};


typedef struct {
    bool valid;     /// is this char valid?
    char value;     /// the content
} Character;


class Terminal {
private:
    bool m_tdmap[256] = { false };          /// token delimiter map ; used for getInputLine
    TMode m_mode;                           /// terminal mode
    struct termios m_terminal {};           /// configuration for terminal behavior
    struct termios m_restore {};            /// behavior before modification
    std::streambuf *m_readbuffer;           /// readbuffer used by singlechar mode

    Character sc_getChar();                 /// sc == single char mode
    String sc_getLine();
    String std_getLine();                   /// std == standard mode

public:
    Terminal();
    ~Terminal();
    void enterSCMode();
    void restoreTerminal();
    StringVec getInputLine();
    String joinLine(const StringVec& vector);

};


#endif //NDS_TERMINAL_H
