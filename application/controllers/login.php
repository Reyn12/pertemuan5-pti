<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_user');
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index()
    {
        if ($this->session->userdata('level') == 'Admin') {
            redirect('admin', 'refresh');
        } elseif ($this->session->userdata('level') == 'Petugas') {
            redirect('petugas', 'refresh');
        } else {
            $this->load->view('login');
        }
    }

    function login_act()
    {
        $email = $this->input->post('email');
        $password = md5($this->input->post('password'));
        $cekEmailUser = $this->m_user->getEmailUser($email);
        $cekPassUser = $this->m_user->getPassUser($password);

        if ($this->input->method() != 'post') {
            redirect('login');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->session->set_flashdata('peringatan', 'Format email salah');
        } elseif ($cekEmailUser->num_rows() == NULL) {
            $this->session->set_flashdata('peringatan', 'Email tidak ditemukan');
        } elseif ($cekPassUser->num_rows() == NULL) {
            $this->session->set_flashdata('peringatan', 'Password Salah');
        } elseif ($cekEmailUser->num_rows() != NULL && $cekPassUser->num_rows() != NULL) {
            foreach ($cekEmailUser->result() as $data) {
                $data_user['id'] = $data->idUser;
                $data_user['nama'] = $data->nama;
                $data_user['email'] = $data->email;
                $data_user['level'] = $data->level;
                $this->session->set_userdata($data_user);

                if ($data->level == "Petugas") {
                    redirect('petugas');
                } elseif ($data->level == "Admin") {
                    redirect('admin');
                }
            }
        } else {
            $this->session->set_flashdata('peringatan', 'Password Salah');
        }

        $this->load->view('login');
    }
}
