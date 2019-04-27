//
// Custom Shell
// Pierre & Andrej
//

#include "Shell.h"

Shell::Shell() {

    addCommand(ShellCommand("help",
        [&] (void * p) {
            out("Test Shell");
            out("Please type the number corresponding to the system you want to access, and hit enter.");
            out("The following are built in:");
            for (auto pair : this->commands) {
                out("  " + pair.second.getName());
            }
            out("Use the man command for information on other programs.");
            return true;
        }
    ));

    addCommand(ShellCommand("exit",
        [](void * p) {
            return false;
        }
    ));

    addCommand(ShellCommand("lsh_1",
        [] (void * p) {
            out("You are going to be redirected to System A");
            //FILE *f = popen( "ssh -t -t nds@192.168.40.1 -p 50222", "r" );
            return true;
        }
    ));

    addCommand(ShellCommand("lsh_2",
        [] (void * p) {
            out("You are going to be redirected to System B");
            return true;
        }
    ));

}

// ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---

void Shell::addCommand(ShellCommand cmd) {
    commands.insert(std::make_pair(cmd.getName(), cmd));
}

// ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---

void Shell::call(const String& commandName) {
    commands.find(commandName)->second();
}

// ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---

Shell::~Shell() = default;
