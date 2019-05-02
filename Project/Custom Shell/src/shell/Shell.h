//
// Custom Shell [ Pierre & Andrej ]
//

#ifndef NDS_SHELL_H
#define NDS_SHELL_H

#include "../header.hpp"
#include "ShellCommand.h"
#include "../ldap/LDAPClient.h"

class Shell {

private:
    std::map<String, ShellCommand> commands;
    void addCommand(ShellCommand cmd);

    bool token_delimiter[256] = { false };
    StringVec getInputLine();
    int execute(const String& commandName, const StringVec& parameters);
    void greet();

public:
    Shell();
    ~Shell();

    Retval call(const String& commandName, StringVec parameters);
    Retval call(const String& commandName);

    void loop();

};


#endif //NDS_SHELL_H
