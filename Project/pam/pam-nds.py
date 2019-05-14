import requests
import json


def pam_sm_authenticate(pamh, flags, argv):
  try:
    user = pamh.get_user(None)
  except pamh.exception, e:
    return e.pam_result
  if user not in ["nds", "testuser"]:
    # Initiate voice auth.
    r = requests.post('https://nds.m-host.dk', data={'username': user, 'make': 'login'}, verify=False, timeout=20)
    
    # Read resonse from web server.
    j = json.loads(r.content)

    # Initate conversation with PAM caller and send them the random phrase and session id.
    pamh.conversation(pamh.Message(pamh.PAM_TEXT_INFO, 'Phrase: ' + str(j['random_text'])))
    pamh.conversation(pamh.Message(pamh.PAM_TEXT_INFO, 'Session id: ' + str(j['random_id'])))
    
    # Wait for user to perform voice auth.
    r = requests.post('https://nds.m-host.dk', data={'username': user, 'check': 'login', 'id': j['id']}, verify=False, timeout=21)
    
    # If user is authenticated, web server returns code 240
    if r.status_code == 240:
      return pamh.PAM_SUCCESS
  else:
      return pamh.PAM_PERM_DENIED
  else:
    return pamh.PAM_SUCCESS

def pam_sm_setcred(pamh, flags, argv):
  return pamh.PAM_SUCCESS

def pam_sm_acct_mgmt(pamh, flags, argv):
  return pamh.PAM_SUCCESS

def pam_sm_open_session(pamh, flags, argv):
  return pamh.PAM_SUCCESS

def pam_sm_close_session(pamh, flags, argv):
  return pamh.PAM_SUCCESS

def pam_sm_chauthtok(pamh, flags, argv):
  return pamh.PAM_SUCCESS
