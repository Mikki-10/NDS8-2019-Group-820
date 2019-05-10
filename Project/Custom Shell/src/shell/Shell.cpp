//
// Custom Shell [ Pierre & Andrej ]
//

#include "Shell.h"


Shell::Shell() {

    // Setting up input parsing variables
    for (bool & t : m_tdmap) t = false;
    for (auto & c : " \t\r\n\a") m_tdmap[static_cast<int>(c)] = true;

    // Setting up LDAP-themed items
    m_user = getlogin();
    m_systems = CritSysContainer();
    m_ldap = new LDAPClient();

    try {
        m_ldap->connect();
        for (const auto& group : m_ldap->getRemoteAccessGroups()) {
            m_systems.addAll(m_ldap->getAccessibleSystems(m_user, group));
        }
        m_ldap->disconnect();
    }
    catch(...) {
        throw;
    }

    for (auto& system : m_systems.getNames()) {
        addCommand(ShellCommand(system, "Redirects to system [" + system + "]",
            [=] (StringVec p) {
                unused(p);

                auto sys = m_systems.get(system);

                sout("   You are about to be redirected to [" + system + "]");
                sout("   System description: " + sys.getDesc());
                std::cout << "   Do you with to proceed? (y/n)  ";

                const auto line = getInputLine();
                if (line.size() == 1 && (line.at(0) == "y" || line.at(0) == "Y")) {
                    sout("   Redirecting... TO BE DONE");
                }
                else {
                    sout("   Redirection aborted.");
                }

                return true;
            }
        ));
    }

    this->greet();
    this->setupCustomCommands();

}


Shell::~Shell() = default;


void Shell::addCommand(ShellCommand cmd) {
    m_commands.insert(std::make_pair(cmd.getName(), cmd));
}


Retval Shell::call(const String &commandName, StringVec parameters) {

    auto cmd = m_commands.find(commandName);
    if (cmd != m_commands.end()) {
        return cmd->second(parameters) ? Retval::SUCCESS : Retval::FAILURE;
    }
    else {
        return Retval::NOT_FOUND;
    }

}

// ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---


StringVec Shell::getInputLine() {

    StringVec res;
    String line, token;
    std::getline(std::cin, line);

    for (auto &c : line) {

        // iterate over each char and check for delimiter
        if (c >= 0 && m_tdmap[static_cast<int>(c)]) {
            if (!token.empty()) {
                res.push_back(token);
                token.clear();
            }
        }
        else token += c;
    }
    if (!token.empty()) {
        res.push_back(token);
    }
    return res;

}


void Shell::loop() {

    int status = 1;
    do {
        std::cout << ":> ";

        StringVec line = getInputLine();
        if (line.empty()) continue;

        String cmd = line.at(0);
        line.erase(line.begin());

        status = execute(cmd, line);

    } while (status);

}


int Shell::execute(const String& commandName, const StringVec& parameters) {

    Retval r = call(commandName, parameters);
    if (r == Retval::NOT_FOUND) {
        sout("Command not found. Please type \"help\" to see list of available commands.");
        return Retval::SUCCESS;
    }
    return r;

}


void Shell::greet() {

    sout("");
    using namespace std;

    short size = 75; // real length of wall
    auto fill = [size](String line) {
        int len = size - line.length();
        for (int i = 0; i < len; i++) {
            std::cout << " ";
        }
    };

    String off("   ");
    String wall("═══════════════════════════════════════════════════════════════════════════");

    StringVec motd;
    motd.push_back(" Welcome to jump server, " + m_user);
    motd.push_back(" This is JumpShell, created by NDS8") ;
    motd.push_back(" To see list of available commands, use \"help\".");
    motd.push_back(" Please choose the specified critical system you are authorised to access:");

    auto names = m_systems.getNames();
    if (names.empty()) {
        motd.push_back(" <!> None systems available. Please contact your administrator.");
    }
    else {
        for (auto &sys : m_systems.getNames()) {
            motd.push_back("   <" + sys + ">");
        }
    }

    // ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---

    for (size_t i = 0; i < motd.size(); i++) {
        if (i == 0) cout << off << "╔" << wall << "╗" << endl;
        if (i == 3) cout << off << "╠" << wall << "╣" << endl;
        cout << off << "║" << motd[i]; fill(motd[i]); cout << "║" << endl;
        if (i == motd.size() - 1) cout << off << "╚" + wall + "╝" << endl;
    }
    sout("");

}


void Shell::setupCustomCommands() {

    String quitDesc = "Disconnects from the jumpserver";
    addCommand(ShellCommand("exit", quitDesc, [](StringVec p) { unused(p); return false; }));
    addCommand(ShellCommand("quit", quitDesc, [](StringVec p) { unused(p); return false; }));

    addCommand(ShellCommand("help", "Prints available commands",
        [=] (StringVec p) {
            unused(p);
            String off("   ");
            sout("   ╔══════════════════╗");
            sout("   ║  NDS8 JumpShell  ║");
            sout("   ╚══════════════════╝");
            sout(off + "The following commands are built in / available:");
            for (auto pair : this->m_commands) {
                sout(off + "  " + pair.second.getName() + " : " + pair.second.getDesc());
            }
            sout("");
            return true;
        }
    ));

    addCommand(ShellCommand("debug", "argument: the name of critical system",
        [=] (StringVec p) {
            try {
                auto sys = m_systems.get(p.at(0));
                sout("Printing debug info about: " + sys.getName());
                sout("IP: " + sys.getAddr());
                sout("Desc: " + sys.getDesc());
            }
            catch(std::invalid_argument &e) {
                sout(e.what());
            }

            return true;
        }
    ));

    addCommand(ShellCommand("jump", "debugging command: jumps to nds@192.168.40.1:50222",
        [=] (StringVec p) {
            unused(p);

            {   // New block in order to destroy the process and have a chance to do something after
                ProcessStream process;
                process.open("ssh nds@192.168.40.1 -p 50222 -i /creds/id_rsa");

                char c;
                String quitphrase("logout");
                unsigned iterator = 0;

                while (true) {
                    c = static_cast<char>(process.out().get());
                    std::cout << c << std::flush;

                    // TODO: possibility to store everything into file (what happened here)
                    // TODO: Log into 127.0.0.1 : unknown port

                    if (c == quitphrase[iterator]) {
                        iterator++;
                        if (iterator == quitphrase.length()) {
                            break;
                        }
                    }
                    else iterator = 0;
                }
                std::cout << std::endl << std::flush;
            }

            sout("");
            sout("   ╔══════════════════════════╗");
            sout("   ║  Returning to JumpShell  ║");
            sout("   ╚══════════════════════════╝");
            sout("");
            return true;

        }
    ));

}
