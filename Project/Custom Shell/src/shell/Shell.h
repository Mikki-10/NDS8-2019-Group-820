//
// Custom Shell [ Pierre & Andrej ]
//

#ifndef NDS_SHELL_H
#define NDS_SHELL_H

#include "../header.hpp"
#include "CritSys.h"
#include "ShellCommand.h"
#include "../ldap/LDAPClient.h"
#include "../thirdparty/pstream/pstream.h"

typedef redi::ipstream ProcessStream;

class Shell {

private:
    /** attr: token delimiter map ; used for getInputLine */
    bool m_tdmap[256] = { false };

    /** attr: custom command container */
    std::map<String, ShellCommand> m_commands;

    /** attr: ldap client object */
    LDAPClient *m_ldap;

    /** Username of current visitor of the jump server */
    String m_user;

    /** Container of critical systems that user can access */
    CritSysContainer m_systems;

    // ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---

    /** @return captured words from user input (stdin) */
    StringVec getInputLine();

    /** @param cmd custom command to be added to member container */
    void addCommand(ShellCommand cmd);

    /** executes given custom command with params */
    int execute(const String& commandName, const StringVec& parameters);

    /** performs greeting to the user */
    void greet();

    /** obvious enough */
    void setupCustomCommands();

    /** this too */
    Retval call(const String& commandName, StringVec parameters);

public:
    Shell();
    ~Shell();
    void loop();

};


#endif //NDS_SHELL_H
