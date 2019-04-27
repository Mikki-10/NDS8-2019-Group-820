//
// Custom Shell [ Pierre & Andrej ]
//

#ifndef NDS_SHELL_H
#define NDS_SHELL_H

#include "header.hpp"
#include "ShellCommand.h"

class Shell {

private:
    std::map<String, ShellCommand> commands;
    void addCommand(ShellCommand cmd);
public:
    void call(const String& commandName);
    Shell();
    ~Shell();

};


#endif //NDS_SHELL_H
