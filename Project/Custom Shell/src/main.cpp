///
/// Custom Shell: the JumpShell
/// Made by Andrej, Pierre and Sebastian
///

#include "header.hpp"
#include "shell/Shell.h"

int main() {
    try {
        Shell().loop();
    }
    catch(LDAPException &e) {
        sout(e);
    }
}
