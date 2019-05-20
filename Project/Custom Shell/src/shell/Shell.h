///
/// Custom Shell: the JumpShell
/// Made by Andrej, Pierre and Sebastian
///

#ifndef NDS_SHELL_H
#define NDS_SHELL_H

#include "../header.hpp"
#include "CritSys.h"
#include "Terminal.h"
#include "ShellCommand.h"
#include "../logging/Logger.h"
#include "../ldap/LDAPClient.h"
#include "../thirdparty/pstream/pstream.h"


typedef redi::ipstream ProcessStream;

class Shell {

private:
    Terminal m_terminal;                            /// Handler to terminal stdio
    Logger m_logger;                                /// Handler to logging object
    std::map<String, ShellCommand> m_commands;      /// Custom command container
    LDAPClient *m_ldap;                             /// LDAP client object
    String m_user;                                  /// Username of current visitor of the jump server
    CritSysContainer m_systems;                     /// Container of critical systems that user can access

    // ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---

    /// @param cmd custom command to be added to member container
    void addCommand(ShellCommand cmd);

    /// executes given custom command with params
    int execute(const String& commandName, const StringVec& parameters);

    /// performs greeting to the user
    void greet();

    /// obvious enough
    void setupCustomCommands();

    /// this too
    Retval call(const String& commandName, StringVec parameters);

    /// not jumping into void tho
    void jump(CritSys &system);

public:
    Shell();
    ~Shell();
    void loop();

};


#endif //NDS_SHELL_H
