//
// Custom Shell [ Pierre & Andrej ]
//

#include "header.hpp"
#include "shell/Shell.h"
#include "ldap/LDAPClient.h"

int main() {
    try {
        Shell().loop();
    }
    catch(LDAPException &e) {
        out(e);
    }
}
